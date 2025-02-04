<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Guarantor;
use App\Loan;
use App\Company;
use App\Payregs;
use App\PayregLoan;
use App\LoanType;
use App\PayregInstruction;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $loans = Loan::all();
        return view('loans.loan_index', array(
            'header' => 'Loans',
            'loans' => $loans,
        ));
    }
    public function loan_reg(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $search = $request->search;
        $loans = Loan::with('loan_type', 'employee','pay')
            ->whereHas('employee', function($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ;
            })
            ->paginate(10);
        $loanTypes = LoanType::all();
        $employees = Employee::where('status', 'Active')->whereIn('company_id', $allowed_companies)->get();
        
        return view('loans.loan_register', array(
            'header' => 'Payroll',
            'loans' => $loans,
            'loanTypes' => $loanTypes,
            'employees' => $employees,
            'search' => $search,
        ));
    }

    public function store_loanReg(Request $request)
    {
        // dd($request->all());
        // Validation
        $this->validate($request, [
            'loan_type' => 'required',
            'employee' => 'required',
            'frequency' => 'required',
            'amount' => 'required', 'min:1',
            'monthly_ammort_amt' => 'required', 'min:1',
            'start_date' => 'required',
            'expiry_date' => 'required|date|after:start_date',
            'initial_amount' => 'required', 'min:1',
        ]);

        $loans = new Loan;
        $loans->loan_type_id = $request->loan_type;
        $loans->employee_id = $request->employee;
        $loans->amount = $request->amount;
        $loans->monthly_ammort_amt = $request->monthly_ammort_amt;
        $loans->initial_amount = $request->initial_amount;
        $loans->start_date = $request->start_date;
        $loans->expiry_date = $request->expiry_date;
        $loans->schedule = $request->frequency;
        $loans->status = "Active";
        $loans->encoded_by = auth()->user()->id;
        $loans->save();

        if ($request->has('loan_beneficiaries'))
        {
            foreach($request->loan_beneficiaries as $loanBeneficiaries) {
                $loan_beneficiaries = new Guarantor;
                $loan_beneficiaries->loan_id = $loans->id;
                $loan_beneficiaries->employee_id = $loanBeneficiaries;
                $loan_beneficiaries->save();
            }
        }

        Alert::success('Successfully Stored')->persistent('Dismiss');
        return back();
    }

    public function updateloanReg(Request $request, $id)
    {
        $loans = Loan::findOrFail($id);
        $loans->loan_type_id = $request->loan_type;
        // $loans->employee_id = $request->employee;
        // $loans->amount = $request->amount;
        $loans->monthly_ammort_amt = $request->monthly_ammort_amt;
        $loans->initial_amount = $request->initial_amount;
        $loans->start_date = $request->start_date;
        $loans->expiry_date = $request->expiry_date;
        $loans->schedule = $request->frequency;
        $loans->status = $request->status;
        $loans->save();

        if ($request->has('loan_beneficiaries'))
        {
            $loan_beneficiaries = Guarantor::where('loan_id', $id)->delete();
            
            foreach($request->loan_beneficiaries as $guarantor) {
                $loan_beneficiaries = new Guarantor;
                $loan_beneficiaries->loan_id = $id;
                $loan_beneficiaries->employee_id = $guarantor;
                $loan_beneficiaries->save();
            }
        }

        Alert::success('Successfully Updated')->persistent('Dismiss');
        return back();
    }
    public function loan_report(Request $request)
    {
        $loans = LoanType::get();

        $pay_regs_instruction = PayregInstruction::select('instruction_name')->where('amount', '<', 0)->groupBy('instruction_name')->get();
        
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();
        $loanType = [];
        if ($request->loan)
        {
            $loanType = $request->loan;
        }
        // dd($loanType);
        $company = $request->company;
        $from = $request->from;
        $to = $request->to;

        $company_array = [];
        if ($company) 
        {
            $company_array = $company;
        }
        
        $pay_register_ids = Payregs::whereBetween('pay_period_from', [$request->from, $request->to])
        ->whereIn('company_id', $company_array)
        ->pluck('id')
        ->toArray();

        $loan_all = PayregLoan::whereIn('payreg_id',$pay_register_ids)->whereIn('loan_type_id',$loanType)->get();
        $pay_regs = PayregInstruction::whereIn('payreg_id',$pay_register_ids)->whereIn('instruction_name',$loanType)->get();
        // dd($loanType, $pay_regs);
        
        return view('reports.loan_report', array(
            'header' => 'reports',
            'companies' => $companies,
            'company' => $company,
            'from' => $from,
            'to' => $to,
            'loans' => $loans,
            'loan_all' => $loan_all,
            'payRegs' => $pay_regs_instruction,
            'pay_regs' => $pay_regs,
            'loanType' => $loanType
        ));
    }
    public function companyLoan(Request $request)
    {
        $loans = LoanType::get();
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();
        $company = $request->companies;
        $loan_type = $request->loans;
        if($request->companies == null)
        {
            $company = [];
        }
        if($request->loans == null)
        {
            $loan_type = [];
        }
        $companies_selected = Company::whereIn('id', $company)->get();
        $loan_all = Loan::with([
            'employee',
            'pay' => function($query)use($request) {
                $query->whereHas('pay_reg', function ($q)use($request) {
                    $q->where('pay_period_to', '<=', $request->as_of); // Filter to only include pay records that have pay_reg
                });
            },
            'pay.pay_reg', // This still loads pay_reg, but only for filtered pay records
        ])
        ->with(['refund' => function($query)use($request) {
            $query->whereHas('pay_reg', function ($q)use($request) {
                $q->where('pay_period_to', '<=', $request->as_of); // Filter to only include pay records that have pay_reg
            });
        }])
        ->where('status', 'Active')
        ->whereIn('loan_type_id', $loan_type)
        ->whereHas('employee', function($query) use ($company) {
            $query->whereIn('company_id', $company);
        })
        ->get();
        // $loan_all = Loan::whereIn('loan_type_id',$request->loans)->where('status','Active')->get();
        return view('reports.company_loan_report', array(
            'header' => 'reports',
            'companies' => $companies,
            'company' => $company,
            'loan_type' => $loan_type,
            'loans' => $loans,
            'loan_all' => $loan_all,
            'companies_selected' => $companies_selected,
            'as_of' => $request->as_of
        ));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loan $loan)
    {
        //
    }
}

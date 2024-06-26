<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TaxController extends Controller
{
    public function tax()
    {
        $taxes = Tax::all();
        return view('taxes.tax',
        array(
            'header' => 'Tax',
            'taxes' => $taxes,
        )
        );
    }

    public function new(Request $request)
    {
        $new_tax = new Tax;
        $new_tax->from_gross = $request->from_gross;
        $new_tax->to_gross = $request->to_gross;
        $new_tax->excess_over = $request->excess_over;
        $new_tax->tax_plus = $request->tax_plus;
        $new_tax->percentage = $request->percentage;
        $new_tax->save();
    
        Alert::success('Successfully Stored')->persistent('Dismiss');
        return back();
    } 
    public function edit_tax(Request $request, $id)
    {
        $new_tax = Tax::findOrFail($id);
        $new_tax->from_gross = $request->from_gross;
        $new_tax->to_gross = $request->to_gross;
        $new_tax->excess_over = $request->excess_over;
        $new_tax->tax_plus = $request->tax_plus;
        $new_tax->percentage = $request->percentage;
        $new_tax->save();

        Alert::success('Successfully Updated')->persistent('Dismiss');
        return back();
    }
    
    public function delete_tax($id)
    {
        $tax = Tax::findOrFail($id); 
        $tax->delete(); 
        return back()->with('status', 'Tax deleted successfully!');
    }

    public function compute_tax()
{
    $employee_salary = 470000;
    $computed_salary = compute_tax($employee_salary);

    return view('taxes.compute_tax', [
        'header' => 'Tax',
        'computed_taxes' => $computed_salary,
    ]);
}
}

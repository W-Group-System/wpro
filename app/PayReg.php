<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class PayReg extends Model
{
    protected $table = 'pay_regs';
    public $fillable = ['payroll_date','total_gross_pay','tax_total','total_deduction', 'net_pay_total'];
}
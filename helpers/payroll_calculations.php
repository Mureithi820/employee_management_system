<?php
function calculate_paye($gross_salary) {
    // Define PAYE tax bands and rates for Kenya
    if ($gross_salary <= 24000) {
        return $gross_salary * 0.1;
    } elseif ($gross_salary <= 32333) {
        return $gross_salary * 0.25;
    } else {
        return $gross_salary * 0.3;
    }
}

function calculate_nhif($gross_salary) {
    // NHIF deductions based on Kenyan rates (example range)
    if ($gross_salary <= 5999) return 150;
    elseif ($gross_salary <= 7999) return 300;
    // Add more brackets as per current NHIF rates
    else return 1700;
}

function calculate_nssf($gross_salary) {
    // NSSF standard rate (this can be adjusted per Kenya's latest rates)
    return min($gross_salary * 0.06, 1080); // Maximum of 1080
}

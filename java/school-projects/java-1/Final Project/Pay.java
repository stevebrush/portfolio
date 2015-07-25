/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 5, Exercise # 6.
 * The Pay class calculates gross pay, total deductions, and net pay for an employee.
 */
import javax.swing.JOptionPane;
import java.util.*;
import java.text.*;
public class Pay
{
    /**
     * Additional Requirements:
     * ------------------------
     * 1) Use three JOptionPane Confirm Dialog Boxes (using the JOptionPane.YES_NO_OPTION)
     *    to ask the worker if they want medical insurance, dental insurance, and
     *    long-term disability insurance, as the worker can enroll in more than one
     *    insurance option.
     * 2) Use a do...while() loop to ask the user for their skill level. Keep looping
     *    until a valid skill level is provided.
     * 3) Use a JOptionPane to show the worker’s gross pay.
     */

    private static int skillLevel;
    private static float payRate;
    private static float hoursWorked;
    private static float overtimeHours;
    private static float regularHours;
    private static float regularPay;
    private static float overtimePay;
    private static float grossPay;
    private static float netPay;
    private static float totalDeductions;
    private static String itemizedDeductions;
    private static boolean retirementPlanActive;
    private static JOptionPane pane;
    private static final float MAX_NORMAL_HOURS_PER_WEEK = 40.0F;

    public static void main(String[] args)
    {
        pane = new JOptionPane();
        totalDeductions = 0.0F;
        retirementPlanActive = false;
        itemizedDeductions = "";
        fetchSkillLevel();
    }

    /**
     * Requests the skill level of the employee.
     */
    private static void fetchSkillLevel()
    {
        String input = "";
        boolean isValid = false;
        do
        {
            input = "" + pane.showInputDialog(null, "Employee skill level (Enter 1-3):");
            input = input.trim();
            if (!input.equals("null"))
            {
                try
                {
                    // Attempt to convert the input into an integer.
                    skillLevel = Integer.parseInt(input);
                    isValid = isSkillLevelValid();
                }
                catch (Exception e)
                {}
                if (!isValid)
                {
                    pane.showMessageDialog(null, "[ERROR]\nThe skill level entered is invalid.\nPlease enter a number between 1 and 3.");
                }
            }
            else
            {
                break;
            }
        }
        while (!isValid);
        if (isValid)
        {
            // Assign the appropriate pay rate.
            assignPayRate();
            fetchHoursWorked();
        }
    }

    /**
     * Requests the number of hours worked and makes sure the entry is valid.
     */
    private static void fetchHoursWorked()
    {
        String input = "";
        boolean isValid = false;
        do
        {
            input = "" + pane.showInputDialog(null, "Hours worked? (may enter quarter-hours):");
            input = input.trim();
            if (!input.equals("null"))
            {
                try
                {
                    // Attempt to convert the input into a float value.
                    hoursWorked = Float.parseFloat(input);
                    isValid = isHoursWorkedValid();
                }
                catch (Exception e)
                {}
                if (!isValid)
                {
                    pane.showMessageDialog(null, "[ERROR]\nThe hours entered are invalid.\nPlease enter a number greater than zero (0).");
                }
            }
            else
            {
                break;
            }
        }
        while (!isValid);
        if (isValid)
        {
            calculateHoursWorked();
            fetchInsuranceOptions();
            calculatePay();
            printResults();
        }
    }

    /**
     * Requests insurance and retirement options depending on the employee's level.
     */
    private static void fetchInsuranceOptions()
    {
        int choice = 0;

        // Only folks with a skill level greater than 1 can opt for insurance.
        if (skillLevel != 1)
        {
            // Medical.
            choice = pane.showConfirmDialog(null, "Employee elects Medical Insurance?", "Insurance", JOptionPane.YES_NO_OPTION);
            if (choice == pane.YES_OPTION) {
                totalDeductions += 32.50F;
                itemizedDeductions += "Medical Insurance:  ($32.50)\n";
            }

            // Dental.
            choice = pane.showConfirmDialog(null, "Employee elects Dental Insurance?", "Insurance", JOptionPane.YES_NO_OPTION);
            if (choice == pane.YES_OPTION)
            {
                totalDeductions += 20.00F;
                itemizedDeductions += "Dental Insurance:  ($20.00)\n";
            }

            // Long-term Disability.
            choice = pane.showConfirmDialog(null, "Employee elects Long-term Disability Insurance?", "Insurance", JOptionPane.YES_NO_OPTION);
            if (choice == pane.YES_OPTION)
            {
                totalDeductions += 10.00F;
                itemizedDeductions += "Long-term Disability. Insurance:  ($10.00)\n";
            }
        }

        // Only skill level 3 gets a retirement option.
        if (skillLevel == 3)
        {
            choice = pane.showConfirmDialog(null, "Retirement plan active?", "Retirement Plan", JOptionPane.YES_NO_OPTION);
            retirementPlanActive = (choice == pane.YES_OPTION);
        }
    }

    /**
     * Calculates all types of pay, including:
     * gross, regular, overtime, net
     */
    private static void calculatePay()
    {
        regularPay = regularHours * payRate;
        overtimePay = overtimeHours * (payRate + (payRate / 2.0F));
        grossPay = regularPay + overtimePay;

        // Only calculate retirement plan deductions if applicable.
        if (retirementPlanActive)
        {
            float retirementCost = (grossPay * 0.03F);
            totalDeductions += retirementCost;
            itemizedDeductions += "Retirement Plan: (" + formatPrice(retirementCost) + ")";
        }
        netPay = (regularPay + overtimePay) - totalDeductions;
    }

    /**
     * Determines how many hours are regular and how many are considered overtime.
     */
    private static void calculateHoursWorked()
    {
        // Overtime?
        if (hoursWorked > MAX_NORMAL_HOURS_PER_WEEK)
        {
            overtimeHours = hoursWorked - MAX_NORMAL_HOURS_PER_WEEK;
            regularHours = MAX_NORMAL_HOURS_PER_WEEK;
        }
        else
        {
            regularHours = hoursWorked;
            overtimeHours = 0.0F;
        }
    }

    /**
     * Returns 'true' if the skill level entered by the user is a valid value.
     */
    private static boolean isSkillLevelValid()
    {
        boolean isValid;
        switch (skillLevel)
        {
            case 1:
            case 2:
            case 3:
            isValid = true;
            break;
            default:
            isValid = false;
            break;
        }
        return isValid;
    }

    /**
     * Returns true or false if the number of hours worked is valid.
     */
    private static boolean isHoursWorkedValid()
    {
        return (hoursWorked > 0.0F);
    }

    /**
     * Based on the skill level entered, assign the appropriate pay rate.
     */
    private static void assignPayRate()
    {
        switch (skillLevel)
        {
            case 1:
            payRate = 17.00F;
            break;
            case 2:
            payRate = 20.00F;
            break;
            case 3:
            payRate = 22.00F;
            break;
            default:
            break;
        }
    }

    /**
     * Prints the pay results to the screen.
     */
    private static void printResults()
    {
        String message = "";
        message += "Hours worked: " + hoursWorked + "\n";
        message += "Hourly pay rate: " + formatPrice(payRate) + "\n";
        message += "Regular pay: " + formatPrice(regularPay) + "\n";
        message += "Overtime pay: " + formatPrice(overtimePay) + "\n";
        message += "-----------------------------------------------\n";
        message += "GROSS: " + formatPrice(grossPay) + "\n";
        if (itemizedDeductions != "")
        {
            message += "-----------------------------------------------\n";
            message += "DEDUCTIONS:\n";
            message += itemizedDeductions + "\n";
        }
        message += "-----------------------------------------------\n";
        if (grossPay < totalDeductions)
        {
            message += "[ERROR] Total deductions excede weekly pay!\n";
        }
        else
        {
            message += "NET: " + formatPrice(netPay) + "\n";
        }
        message += "-----------------------------------------------\n";
        pane.showMessageDialog(null, message);
    }

    /**
     * Returns a formatted currency string based on a float value.
     */
    private static String formatPrice(float price)
    {
        DecimalFormat decimalFormat = new DecimalFormat();
        decimalFormat.setMaximumFractionDigits(2);
        decimalFormat.setMinimumFractionDigits(2);
        return "$" + decimalFormat.format(price);
    }
}

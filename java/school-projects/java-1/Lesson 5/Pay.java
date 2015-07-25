/**
 * @author Steve Brush.
 * Lesson 5, Excercise # 6.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 26
 * The Pay class calculates gross pay, total deductions, and net pay for an employee.
 */
import java.util.*;
import java.text.*;

public class Pay
{
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
    private static Scanner scanner;
    private static final float MAX_NORMAL_HOURS_PER_WEEK = 40.0F;

    public static void main(String[] args)
    {
        scanner = new Scanner(System.in);
        startInquiry();
    }

    /**
     * Collects all employee information.
     */
    private static void startInquiry()
    {
        // Reset values.
        totalDeductions = 0.0F;
        retirementPlanActive = false;
        itemizedDeductions = "";

        // Skill level.
        System.out.print("Employee skill level:\n  [Enter 1-3] >>");
        skillLevel = scanner.nextInt();
        scanner.nextLine();

        // Make sure the skill level is a valid number.
        if (isSkillLevelValid())
        {

            // Assign the appropriate pay rate.
            assignPayRate();

            // Hours worked.
            System.out.print("Hours worked? (may enter quarter-hours) >>");
            while (!scanner.hasNextFloat())
            {
                // Do something with bad value, e.g.
                System.out.println("Bad value");
                scanner.nextLine();
            }
            hoursWorked = scanner.nextFloat();
            scanner.nextLine();

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

            // Insurance.
            if (skillLevel != 1)
            {
                System.out.print("Insurances elected (separate multiple choices with commas):\n  1) Medical  2) Dental  3) Long-term Disability  RETURN) Quit >>");
                String[] insuranceChoices = scanner.nextLine().split(",");
                int length = insuranceChoices.length;

                // For each insurance option entered, add to the total deductions amount.
                for (int i = 0; i < length; i++)
                {
                    switch (insuranceChoices[i])
                    {
                        case "1":
                        totalDeductions += 32.50F;
                        itemizedDeductions += "      Medical Insurance:  ($32.50)\n";
                        break;
                        case "2":
                        totalDeductions += 20.00F;
                        itemizedDeductions += "       Dental Insurance:  ($20.00)\n";
                        break;
                        case "3":
                        totalDeductions += 10.00F;
                        itemizedDeductions += "      LT Dis. Insurance:  ($10.00)\n";
                        break;
                        default:
                        totalDeductions = 0.0F;
                        break;
                    }
                }
            }

            // Retirement.
            if (skillLevel == 3)
            {
                System.out.print("Retirement plan active?\n  1) Yes  2) No >>");
                retirementPlanActive = (scanner.nextInt() == 1);
            }
            calculatePay();
            printResults();
        }
        else
        {
            System.out.println("[ERROR] The skill level entered is invalid. Please enter a number between 1 and 3.");
        }

        // Restart the inquiry.
        startInquiry();
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
            itemizedDeductions += "        Retirement Plan:  (" + formatPrice(retirementCost) + ")";
        }
        netPay = (regularPay + overtimePay) - totalDeductions;
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
        System.out.println("==================================================");
        System.out.println("--------------------------------------------------");
        System.out.println("==================================================");
        System.out.println("           Hours worked:  " + formatPrice(hoursWorked));
        System.out.println("        Hourly pay rate:  " + formatPrice(payRate));
        System.out.println("            Regular pay:  " + formatPrice(regularPay));
        System.out.println("           Overtime pay:  " + formatPrice(overtimePay));
        System.out.println("--------------------------------------------------");
        System.out.println("                  GROSS:  " + formatPrice(grossPay));
        if (itemizedDeductions != "")
        {
            System.out.println("--------------------------------------------------");
            System.out.println("             DEDUCTIONS:");
            System.out.println(itemizedDeductions);
        }
        System.out.println("==================================================");
        if (grossPay < totalDeductions)
        {
            System.out.println("[ERROR] Total deductions excede weekly pay!");
        }
        else
        {
            System.out.println("                    NET:  " + formatPrice(netPay));
        }
        System.out.println("==================================================");
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

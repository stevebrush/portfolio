/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 8.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The NextMonth class calculates the number of days remaining in this month, as well
 * as calculating the number of days until next year's July 21.
 */
import java.util.*;
public class NextMonth
{
    public static void main(String[] args)
    {
        // Today.
        GregorianCalendar today = new GregorianCalendar();

        // June 21, this year.
        GregorianCalendar calTest = new GregorianCalendar(today.get(Calendar.YEAR), 5, 21);

        // June 21, next year.
        GregorianCalendar calTestNextYear = new GregorianCalendar((calTest.get(Calendar.YEAR) + 1), 5, 21);

        // Allocate today's properties.
        int monthMaxDays = today.getActualMaximum(Calendar.DAY_OF_MONTH);
        int currentDayOfYear = today.get(Calendar.DAY_OF_YEAR);
        int numDaysInYear = today.getActualMaximum(Calendar.DAY_OF_YEAR);
        int numDaysInYearRemaining;

        // Get the number of days until the first of next month.
        System.out.println("Days remaining in this month: " + (monthMaxDays - today.get(Calendar.DAY_OF_MONTH)));

        // If today’s date is beyond June 21, calculate the remaining days left
        // in the year, plus the time from January 1 to June 21 of the next year.
        if (currentDayOfYear > calTest.get(Calendar.DAY_OF_YEAR)) {
            numDaysInYearRemaining = numDaysInYear - currentDayOfYear;
            System.out.println("Today's day is beyond June 21.");
            System.out.println("Number of days remaining in this year: " + numDaysInYearRemaining);
            System.out.println("Number of days from January 1 to June 21, next year: " + calTestNextYear.get(Calendar.DAY_OF_YEAR));
            System.out.println("Total number of days from today until June 21 of next year: " + (numDaysInYearRemaining + calTestNextYear.get(Calendar.DAY_OF_YEAR)));
        } else {
            System.out.println("Today's day is before June 21.");
        }
    }
}

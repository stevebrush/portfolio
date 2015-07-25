/**
 * @author Steve Brush.
 * Lesson 5, Excercise # 15.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 27
 * The CreateBabysittingJob class collects the various information for a new
 * babysitting job and prints the results on the screen.
 */
import java.util.*;
public class CreateBabysittingJob
{
    public static void main(String[] args)
    {
        Scanner scanner = new Scanner(System.in);
        boolean hasError = false;
        int year = 0;
        int jobId = 0;
        int jobNumber = 0;
        int employeeId = 0;
        int numChildren = 0;
        int numHours = 0;
        int requiresDiaperChanging = 0;

        // Fetch the year.
        do
        {
            if (hasError)
            {
                System.out.println("Invalid entry. Please enter an integer between 2013 and 2025.");
            }
            System.out.print("Enter the year (2013-2025) >>");
            year = scanner.nextInt();
            hasError = true;
        }
        while (year < 2013 || year > 2025);

        // Fetch the job number.
        hasError = false;
        do
        {
            if (hasError)
            {
                System.out.println("Invalid entry. Please enter an integer between 0 and 9999.");
            }
            System.out.print("Enter the job number (0-9999) >>");
            jobNumber = scanner.nextInt();
            hasError = true;
        }
        while (jobNumber < 0 || jobNumber > 9999);

        // Fetch the employee ID.
        hasError = false;
        do
        {
            if (hasError)
            {
                System.out.println("Invalid entry. Please enter an integer between 1 and 3.");
            }
            System.out.print("Enter the employee ID (1-3) >>");
            employeeId = scanner.nextInt();
            hasError = true;
        }
        while (employeeId < 1 || employeeId > 3);

        // Fetch the number of children.
        hasError = false;
        do
        {
            if (hasError)
            {
                System.out.println("Invalid entry. Please enter an integer between 1 and 9.");
            }
            System.out.print("Enter the number of children (1-9) >>");
            numChildren = scanner.nextInt();
            hasError = true;
        }
        while (numChildren < 1 || numChildren > 9);

        // Fetch the number of hours.
        hasError = false;
        do
        {
            if (hasError)
            {
                System.out.println("Invalid entry. Please enter an integer between 1 and 12.");
            }
            System.out.print("Enter the number of hours (1-12) >>");
            numHours = scanner.nextInt();
            hasError = true;
        }
        while (numHours < 1 || numHours > 12);

        // Ask user if diaper changing is required.
        hasError = false;
        do
        {
            if (hasError)
            {
                System.out.println("Invalid entry. Please enter an integer between 1 and 2.");
            }
            System.out.print("Diaper changing required? 1) Yes  2) No >>");
            requiresDiaperChanging = scanner.nextInt();
            hasError = true;
        }
        while (requiresDiaperChanging < 1 || requiresDiaperChanging > 2);

        // Create the job ID.
        jobId = BabysittingJob.createJobId(year, jobNumber);

        // Create the babysitting job and display the results.
        BabysittingJob job = new BabysittingJob(jobId, employeeId, numChildren, numHours, (requiresDiaperChanging == 1));
        job.printDetails();
    }
}

/**
 * @author Steve Brush.
 * Lesson 5, Excercise # 15.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 27
 * The BabysittingJob class handles and displays the fee information for a particular babysitting
 * job, based on the employee, the number of children, and the number of hours worked.
 */
public class BabysittingJob
{

    /**
     * @param jobId.
     * Contains six digits.
     * The first two digits represent the year.
     * The last four digits represent a sequential number.
     */
    private int jobId;
    private int employeeId;
    private int numChildren;
    private int numHours;
    private int fee;
    private boolean requiresDiaperChanging;
    private String employeeName;

    public BabysittingJob(int jobId, int employeeId, int numChildren, int numHours, boolean requiresDiaperChanging)
    {
        this.jobId = jobId;
        this.employeeId = employeeId;
        this.numChildren = numChildren;
        this.numHours = numHours;
        this.requiresDiaperChanging = requiresDiaperChanging;
        assignEmployeeName();
        calculateFee();
    }

    /**
     * Assigns a name to the employee based on the employeeId set in the constructor.
     */
    private void assignEmployeeName()
    {
        switch (employeeId)
        {
            case 1:
            employeeName = "Cindy";
            break;
            case 2:
            employeeName = "Greg";
            break;
            case 3:
            employeeName = "Marcia";
            break;
        }
    }

    /**
     * Returns a formatted job ID based on the year and job number provided.
     */
    public static int createJobId(int year, int jobNumber)
    {
        return Integer.parseInt((year % 100) + "" + jobNumber);
    }

    /**
     * Calculates the total fee for the job based on the employeeId set in the constructor.
     */
    private void calculateFee()
    {
        int payRate = 0;

        // Determine pay rate.
        switch (employeeId)
        {
            case 1:
            payRate = 7 * numChildren;
            break;
            case 2:
            case 3:
            for (int i = 0; i < numChildren; ++i)
            {
                if (i == 0) {
                    payRate = 9;
                } else {
                    payRate += 4;
                }
            }
            break;
        }

        // Calculate total fee.
        fee = payRate * numHours;

        // Account for diaper changing.
        if (requiresDiaperChanging) {
            fee += 20;
        }
    }

    /**
     * Prints the details of the job to the user.
     */
    public void printDetails()
    {
        System.out.println("----------------------------------------");
        System.out.println("Babysitting Job Details:");
        System.out.println("========================================");
        System.out.println("Job ID:                    " + jobId);
        System.out.println("Employee name:             " + employeeName);
        System.out.println("Employee ID:               " + employeeId);
        System.out.println("Number of children:        " + numChildren);
        System.out.println("Hours:                     " + numHours);
        System.out.println("Requires diaper changing?  " + requiresDiaperChanging);
        System.out.println("========================================");
        System.out.println("Total Fee:                 $" + fee);
        System.out.println("----------------------------------------");
    }

}

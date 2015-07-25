/**
 * @author Steve Brush.
 * Lesson 6, Excercise # 6.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 27
 * The EverySum class prints the sum of every number between 1 and n.
 */
public class EverySum
{
    public static void main(String[] args)
    {
        int i;
        int sum;
        int counter;
        int max = 50;
        int numIterations = max - 1;

        for (i = 1; i < numIterations; ++i) {

            sum = 0;
            counter = 0;

            // Execute at least once:
            do
            {
                sum += ++counter;
            }
            while (counter < i);

            // Display the results.
            System.out.println("The sum of all integers from 1 to " + i + " is: " + sum);
        }
    }
}

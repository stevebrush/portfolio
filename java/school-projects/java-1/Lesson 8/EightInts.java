/**
 * @author Steve Brush.
 * Lesson 8, Excercise # 1.
 * CIS163AA
 * Class # 21432
 * 2015 May 6
 * The EightInts class displays an array of integers forward and backward.
 */
public class EightInts
{
    public static void main(String[] args)
    {
        int[] intStorage = {0, 5, 4, 2, 1, 9, 8, 5};
        int length = intStorage.length;

        String messageForward = "";
        String messageBackward = "";

        // Loop through the array forward,
        // but also record the index in reverse.
        for (int i = 0; i < length; ++i)
        {
            messageForward += intStorage[i] + " ";
            messageBackward += intStorage[length - 1 - i] + " ";
        }

        // Display the results to the user.
        System.out.println(messageForward);
        System.out.println(messageBackward);
    }
}

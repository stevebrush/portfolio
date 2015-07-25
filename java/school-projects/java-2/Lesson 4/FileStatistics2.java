/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 3.
 * Chapter 13, Exercise # 2.
 * The FileStatistics2 application displays information about a text file and a
 * .doc file, and provides a ratio of their file sizes.
 */
import java.nio.file.*;
import java.nio.file.attribute.*;
import java.io.IOException;
public class FileStatistics2
{
    public static void main(String[] args)
    {
        Path filePathTxt = Paths.get("Quote.txt");
        Path filePathDoc = Paths.get("Quote.doc");
        try
        {
            BasicFileAttributes attrTxt = Files.readAttributes(filePathTxt, BasicFileAttributes.class);
            BasicFileAttributes attrDoc = Files.readAttributes(filePathDoc, BasicFileAttributes.class);

            // Determine the human-readable file size ratio.
            String myRatio = ratio(attrTxt.size(), attrDoc.size());

            // Display the results.
            System.out.println("==========================================================");
            System.out.println("File Information");
            System.out.println("----------------------------------------------------------");
            System.out.println("Name: " + filePathTxt.getFileName().toString());
            System.out.println("Size: " + attrTxt.size() + " bytes");
            System.out.println("----------------------------------------------------------");
            System.out.println("Name: " + filePathDoc.getFileName().toString());
            System.out.println("Size: " + attrDoc.size() + " bytes");
            System.out.println("----------------------------------------------------------");
            System.out.println("Size Ratio: " + myRatio);
            System.out.println("==========================================================");

        }
        catch (IOException error)
        {
            System.out.println("IO Exception: " + error.getMessage());
        }
    }

    /**
     * Returns a String representation of a ratio.
     */
    public static String ratio(long antecedent, long consequent)
    {
        long gcd = greatestCommonDivisor(antecedent, consequent);
        return (antecedent / gcd) + ":" + (consequent / gcd);
    }

    /**
     * Determines two long's greatest common denominator.
     */
    public static long greatestCommonDivisor(long num1, long num2)
    {
        /**
         * Remainder after modulus operation returns zero,
         * meaning, we've found a common divisor.
         */
        if (num2 == 0)
        {
            return num1;
        }
        return greatestCommonDivisor(num2, num1 % num2);
    }
}

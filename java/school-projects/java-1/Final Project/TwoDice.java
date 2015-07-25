/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 4, Game Zone # 1 & 2.
 * The TwoDice application simply creates two Dice objects to test their values.
 */
public class TwoDice
{
    public static void main(String[] args)
    {
        Die die1 = new Die();
        Die die2 = new Die();
        System.out.println("Dice values: " + die1.getValue() + ", " + die2.getValue());
    }
}

/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 4, Game Zone # 1 & 2.
 * The Die class stores the possible values of a dice "roll".
 */
public class Die
{
    private int value;
    private static final int LOWEST_DIE_VALUE = 1;
    private static final int HIGHEST_DIE_VALUE = 6;
    public Die()
    {
        roll();
    }
    /**
     * Creates and sets the random "roll" value of this die object.
     */
    public void roll()
    {
        this.value = ((int)(Math.random() * 100) % HIGHEST_DIE_VALUE + LOWEST_DIE_VALUE);
    }
    /**
     * Getters and Setters.
     */
    public int getValue()
    {
        return this.value;
    }
}

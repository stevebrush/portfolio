/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 1.
 * The RaceHorse class adds fields to the standard Horse class to make the horse
 * behave as a race horse.
 */
public class RaceHorse extends Horse
{
    private int numRacesCompeted;
    private int finishingPosition;

    /**
     * Getters and Setters.
     */
    public int getNumRacesCompeted()
    {
        return numRacesCompeted;
    }
    public void setNumRacesCompeted(int val)
    {
        numRacesCompeted = val;
    }
    public int getFinishingPosition()
    {
        return finishingPosition;
    }
    public void setFinishingPosition(int val)
    {
        finishingPosition = val;
    }
}

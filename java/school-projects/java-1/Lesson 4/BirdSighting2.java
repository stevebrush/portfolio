/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The BirdSighting2 class stores sightings of various species of birds, and when
 * the sightings took place.
 */
public class BirdSighting2
{
    private String species;
    private int sightings;
    private int day;
    public BirdSighting2()
    {
        this("robin", 1, 1);
    }
    public BirdSighting2(String species, int sightings, int day) {
        this.species = species;
        this.sightings = sightings;
        this.day = day;
    }
    /**
     * Getters and setters.
     */
    public String getSpecies()
    {
        return species;
    }
    public int getSightings()
    {
        return sightings;
    }
    public int getDay()
    {
        return day;
    }
}

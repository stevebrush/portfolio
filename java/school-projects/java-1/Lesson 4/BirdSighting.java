/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The BirdSighting class stores sightings of various species of birds, and when
 * the sightings took place.
 */
public class BirdSighting
{
    private String species;
    private int sightings;
    private int day;
    public BirdSighting()
    {
        species = "robin";
        sightings = 1;
        day = 1;
    }
    public BirdSighting(String species, int sightings, int day) {
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

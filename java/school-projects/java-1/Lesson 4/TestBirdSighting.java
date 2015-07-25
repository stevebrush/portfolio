/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The TestBirdSighting class tests the BirdSightings class to make sure the
 * overloaded constructor works appropriately.
 */
public class TestBirdSighting
{
    public static void main(String[] args)
    {
        // With defaults.
        BirdSighting birdSighting = new BirdSighting();
        System.out.println("Species: " + birdSighting.getSpecies());
        System.out.println("Sightings: " + birdSighting.getSightings());
        System.out.println("Day: " + birdSighting.getDay());

        // Add assignments to the constructor.
        BirdSighting birdSightingOverloaded = new BirdSighting("eagle", 2, 10);
        System.out.println("Species: " + birdSightingOverloaded.getSpecies());
        System.out.println("Sightings: " + birdSightingOverloaded.getSightings());
        System.out.println("Day: " + birdSightingOverloaded.getDay());
    }
}

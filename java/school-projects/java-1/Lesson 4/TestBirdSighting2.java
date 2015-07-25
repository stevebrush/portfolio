/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The TestBirdSighting2 class tests the BirdSightings2 class to make sure the
 * overloaded constructor works appropriately.
 */
public class TestBirdSighting2
{
    public static void main(String[] args)
    {
        // With defaults.
        BirdSighting2 birdSighting = new BirdSighting2();
        System.out.println("Species: " + birdSighting.getSpecies());
        System.out.println("Sightings: " + birdSighting.getSightings());
        System.out.println("Day: " + birdSighting.getDay());

        // Add assignments to the constructor.
        BirdSighting2 birdSightingOverloaded = new BirdSighting2("eagle", 2, 10);
        System.out.println("Species: " + birdSightingOverloaded.getSpecies());
        System.out.println("Sightings: " + birdSightingOverloaded.getSightings());
        System.out.println("Day: " + birdSightingOverloaded.getDay());
    }
}

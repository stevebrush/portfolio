/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 1.
 * The DemoHorses application demonstrates the various fields and behaviors for
 * the Horse and RaceHorse classes.
 */
public class DemoHorses
{
    /**
     * Additional Requirements:
     * Add an additional data field to the RaceHorse class that stores
     * the place that the horse came in, including its get() and set()
     * methods. Demonstrate the usage of the various data fields in the
     * DemoHorsesclass by using their get and set method.
     */
    public static void main(String args[]) {

        // Mr. Ed
        Horse farmHorse = new Horse();
        farmHorse.setName("Mr. Ed");
        farmHorse.setColor("brown");
        farmHorse.setBirthYear(1961);

        // Starla
        RaceHorse starla = new RaceHorse();
        starla.setName("Starla");
        starla.setColor("white");
        starla.setBirthYear(1996);
        starla.setNumRacesCompeted(54);
        starla.setFinishingPosition(9);

        // Display the results!
        System.out.println(farmHorse.getName() + " is a " + farmHorse.getColor() + " horse, born in " + farmHorse.getBirthYear() + ".");
        System.out.println(starla.getName() + " is a " + starla.getColor() + " race horse, born in " + starla.getBirthYear() + ", and participated in " + starla.getNumRacesCompeted() + " races--who's rank remains at: #" + starla.getFinishingPosition() + ".");
    }
}

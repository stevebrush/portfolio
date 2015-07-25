/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 3.
 * The UseAuto application demonstrates the use of various types of Auto classes.
 */
public class UseAuto
{
    public static void main(String args[])
    {
        Ford car1 = new Ford();
        car1.setMake("Ford");
        car1.setModel("Ranger");
        car1.setPrice(22000);
        System.out.println(car1.getMake() + " " + car1.getModel() + ": $" + car1.getPrice());

        Ford car3 = new Ford();
        car3.setMake("Ford");
        car3.setModel("Explorer");
        car3.setPrice(25000);
        System.out.println(car3.getMake() + " " + car3.getModel() + ": $" + car3.getPrice());

        Chevy car2 = new Chevy();
        car2.setMake("Chevy");
        car2.setModel("Corvette");
        car2.setPrice(20000);
        System.out.println(car2.getMake() + " " + car2.getModel() + ": $" + car2.getPrice());

        Chevy car4 = new Chevy();
        car4.setMake("Chevy");
        car4.setModel("Sonic");
        car4.setPrice(30000);
        System.out.println(car4.getMake() + " " + car4.getModel() + ": $" + car4.getPrice());
    }
}

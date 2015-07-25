/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 6.
 * The UsePackage application demonstrates the use of the Package and
 * InsuredPackage classes.
 */
public class UsePackage
{
    public static void main(String args[])
    {
        // Standard Packages.
        Package package1 = new Package(3, 'M');
        package1.display();
        Package package2 = new Package(8, 'T');
        package2.display();
        Package package3 = new Package(21, 'A');
        package3.display();

        // Insured Packages.
        InsuredPackage iPackage1 = new InsuredPackage(25, 'A');
        iPackage1.display();
        InsuredPackage iPackage2 = new InsuredPackage(1, 'T');
        iPackage2.display();
        InsuredPackage iPackage3 = new InsuredPackage(10, 'M');
        iPackage3.display();
        InsuredPackage iPackage4 = new InsuredPackage(1, 'M');
        iPackage4.display();
    }
}

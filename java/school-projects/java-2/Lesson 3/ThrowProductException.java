/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 30.
 * Chapter 12, Exercise # 6.
 * The ThrowProductException application tests the Product class and its respective Exception.
 */
public class ThrowProductException
{
    public static int[] productNumbers = {463, 9, 133, 531623};
    public static float[] prices = {5.60F, 0, 12000.0F, 12};
    public static void main(String[] args)
    {
        for (int i = 0; i < 4; ++i)
        {
            createProduct(productNumbers[i], prices[i]);
        }
    }

    /**
     * Creates Product objects and displays messages to user.
     */
    public static void createProduct(int productNum, float price)
    {
        try
        {
            Product prod = new Product(productNum, price);
            System.out.println("----------------------------------");
            System.out.println("-  Product Successfully Created.");
        }
        catch (ProductException error)
        {
            System.out.println("----------------------------------");
            System.out.println("-  Product Creation Error!");
            System.out.println("-  ERROR: " + error.getMessage());
        }
        finally
        {
            System.out.println("-  Product details-->");
            System.out.println("-  Product Number: " + productNum + ",  Price: " + price);
        }
    }
}

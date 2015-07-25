/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 30.
 * Chapter 12, Exercise # 6.
 * The Product class throws ProductException based on productNum and price restrictions.
 */
public class Product
{
    private int productNum;
    private float price;
    public Product(int productNum, float price) throws ProductException
    {
        // If productNum does not have 3 digits, throw an error.
        if (String.valueOf(productNum).length() != 3)
        {
            throw new ProductException("The product number provided does not consist of three digits. You entered: '" + productNum + "'.");
        }
        this.productNum = productNum;

        // If the price is less than 0.01, or over 1000 throw an error.
        if (price < .01F || price > 1000.0F)
        {
            throw new ProductException("The price provided is less than $0.01 or greater than $1,000. You entered: '" + price + "'.");
        }
        this.price = price;
    }
}

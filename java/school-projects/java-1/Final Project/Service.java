/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 9, Exercise # 7.
 * The Service class stores various information for a Salon's service offerings.
 */
import java.util.Comparator;
public class Service
{
    private String description;
    private float price;
    private int duration;
    public Service(String description, float price, int duration)
    {
        this.description = description;
        this.price = price;
        this.duration = duration;
    }

    /**
     * Getters and Setters.
     */
    public String getDescription()
    {
        return this.description;
    }
    public float getPrice()
    {
        return this.price;
    }
    public int getDuration()
    {
        return this.duration;
    }

    /**
     * Returns a Comparator to be used to sort the description.
     */
    public static Comparator<Service> getDescriptionComparator()
    {
        return new Comparator<Service>()
        {
            public int compare(Service service1, Service service2)
            {
                return (service1.getDescription().compareTo(service2.getDescription()));
            }
        };
    }

    /**
     * Returns a Comparator to be used to sort the price.
     */
    public static Comparator<Service> getPriceComparator()
    {
        return new Comparator<Service>()
        {
            public int compare(Service service1, Service service2)
            {
                return (int)(service1.getPrice() - service2.getPrice());
            }
        };
    }

    /**
     * Returns a Comparator to be used to sort the duration.
     */
    public static Comparator<Service> getDurationComparator()
    {
        return new Comparator<Service>()
        {
            public int compare(Service service1, Service service2)
            {
                return (service1.getDuration() - service2.getDuration());
            }
        };
    }
}

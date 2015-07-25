/**
 * @author Steve Brush.
 * Lesson 9, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 May 9
 * The Recording class stores information for individual audio recordings.
 */
import java.util.Comparator;
public class Recording
{
    private String title;
    private String artist;
    private int playTime;
    public Recording(String title, String artist, int playTime)
    {
        this.title = title;
        this.artist = artist;
        this.playTime = playTime;
    }

    /**
     * Getters and setters.
     */
    public String getTitle()
    {
        return this.title;
    }
    public String getArtist()
    {
        return this.artist;
    }
    public int getPlayTime()
    {
        return this.playTime;
    }
    public void setTitle(String value)
    {
        this.title = value;
    }
    public void setArtist(String value)
    {
        this.artist = value;
    }
    public void setPlayTime(int value)
    {
        this.playTime = value;
    }

    /**
     * Returns a Comparator to be used to sort the title.
     */
    public static Comparator<Recording> getTitleComparator()
    {
        return new Comparator<Recording>()
        {
            public int compare(Recording rec1, Recording rec2)
            {
                return (rec1.getTitle().compareTo(rec2.getTitle()));
            }
        };
    }

    /**
     * Returns a Comparator to be used to sort the artist.
     */
    public static Comparator<Recording> getArtistComparator()
    {
        return new Comparator<Recording>()
        {
            public int compare(Recording rec1, Recording rec2)
            {
                return (rec1.getArtist().compareTo(rec2.getArtist()));
            }
        };
    }

    /**
     * Returns a Comparator to be used to sort the play time.
     */
    public static Comparator<Recording> getPlayTimeComparator()
    {
        return new Comparator<Recording>()
        {
            public int compare(Recording rec1, Recording rec2)
            {
                return (rec1.getPlayTime() - rec2.getPlayTime());
            }
        };
    }
}

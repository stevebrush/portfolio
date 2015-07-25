/**
 * @author Steve Brush.
 * Lesson 9, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 May 9
 * The RecordingSort class handles user input and creates Recording objects based
 * on the data entered. It also displays the recordings to the user in various ways.
 */
import java.util.*;
import javax.swing.*;
public class RecordingSort
{
    private static JOptionPane pane;
    private static final int MAX_RECORDINGS = 5;
    public static void main(String[] args)
    {
        /**
         * 1. Ask the user to enter information for five (5) separate recordings.
         * 2. Ask the user to select which field to sort by.
         * 3. Based on the user's selection, sort the recordings accordingly.
         * 4. Display the sorted recordings to the user.
         */

        pane = new JOptionPane();

        String input = "";

        int recordingCounter = 0;
        int propertyCounter = 0;

        ArrayList<Recording> recordings = new ArrayList<Recording>(MAX_RECORDINGS);

        // Recording properties.
        String recordingTitle = "";
        String recordingArtist = "";
        int recordingPlayTime = 0;

        boolean hasPlayTimeError = true;

        while (recordingCounter < MAX_RECORDINGS)
        {
            // Get the title.
            input = getInput("Enter Recording " + (recordingCounter + 1) + " title:");
            if (input.equals("null"))
            {
                recordingCounter = MAX_RECORDINGS;
                break;
            }
            else
            {
                // Set the title.
                recordingTitle = input;

                // Get the artist.
                input = getInput("Enter Recording " + (recordingCounter + 1) + " artist:");
                if (input.equals("null"))
                {
                    recordingCounter = MAX_RECORDINGS;
                    break;
                }
                else
                {
                    // Set the artist.
                    recordingArtist = input;

                    hasPlayTimeError = true;
                    while (hasPlayTimeError)
                    {
                        // Get the play time.
                        input = getInput("Enter Recording " + (recordingCounter + 1) + " play time (seconds):");
                        if (input.equals("null"))
                        {
                            recordingCounter = MAX_RECORDINGS;
                            break;
                        }
                        else
                        {
                            // Convert the response into an Integer and
                            // make sure it is greater than zero.
                            try
                            {
                                recordingPlayTime = Integer.parseInt(input);
                                if (recordingPlayTime > 0)
                                {
                                    hasPlayTimeError = false;
                                }
                            }
                            catch(Exception e)
                            {
                                // Do something with the error.
                            }
                            if (hasPlayTimeError)
                            {
                                pane.showMessageDialog(null, "Please enter a whole number greater than zero.\nYou entered: '" + input + "'");
                            }
                        }
                    }
                    // Add the Recording.
                    recordings.add(new Recording(recordingTitle, recordingArtist, recordingPlayTime));
                }

            }
            // Progress to the next recording.
            ++recordingCounter;
        }

        // Ask the user how they would like to sort the data.
        int choice = 0;
        boolean responseValid = false;
        while (!input.equals("null"))
        {
            input = getInput("How would you like to sort the recordings?\nEnter: 1) by title  2) by artist  3) by play time");
            if (!input.equals("null"))
            {
                // Convert the response into an Integer,
                // And check if it is within range.
                try
                {
                    choice = Integer.parseInt(input);
                    responseValid = (choice == 1 || choice == 2 || choice == 3);
                }
                catch (Exception e)
                {
                    // Do something with the error, here.
                }
                if (!responseValid)
                {
                    pane.showMessageDialog(null, "Please enter only one choice: 1, 2, or 3.\nYou entered: " + input);
                }
                else
                {
                    // Show the presorted results on the console.
                    System.out.println("*** Before sorting:");
                    for (Recording rec : recordings)
                    {
                        System.out.println(rec.getTitle() + ", " + rec.getArtist() + ", " + rec.getPlayTime());
                    }

                    // Sort the results.
                    String sortLabel = "";
                    switch (choice)
                    {
                        case 1:
                        default:
                            sortLabel = "title";
                            Collections.sort(recordings, Recording.getTitleComparator());
                        break;
                        case 2:
                            sortLabel = "artist";
                            Collections.sort(recordings, Recording.getArtistComparator());
                        break;
                        case 3:
                            sortLabel = "play time";
                            Collections.sort(recordings, Recording.getPlayTimeComparator());
                        break;
                    }

                    // Display the sorted results to the user.
                    String message = "Recordings sorted according to " + sortLabel + "\n";
                    for (Recording rec : recordings) {
                        message += rec.getTitle() + ", " + rec.getArtist() + ", " + rec.getPlayTime() + "\n";
                    }
                    pane.showMessageDialog(null, message);
                }
            }
        }
    }

    /**
     * Returns user input as a String.
     */
    private static String getInput(String question)
    {
        String input = "" + pane.showInputDialog(null, question);
        return input.trim();
    }
}

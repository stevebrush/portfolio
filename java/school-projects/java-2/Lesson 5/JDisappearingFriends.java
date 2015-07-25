/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July5.
 * Chapter 14, Exercise # 3.
 * The JDisappearingFriends application demonstrates the use of JFrames and JLabels,
 * hiding/showing a list of names when a button is clicked.
 */
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
public class JDisappearingFriends
{
    public static int currentIndex;
    public static JLabel[] friends;

    public static void main(String[] args)
    {
        JFrame frame = new JFrame("Disappearing Friends");
        JButton button = new JButton("Show a Different Friend");
        String[] friendNames = {"Samwise", "Hamilton", "Parks", "Samantha", "Lacie"};

        currentIndex = 0;

        // Create the five friend labels.
        friends = new JLabel[friendNames.length];
        for (int i = 0; i < friendNames.length; ++i)
        {
            // Create the label.
            friends[i] = new JLabel(friendNames[i]);

            // Show or hide the label based on the current index.
            if (i == currentIndex)
            {
                friends[i].setVisible(true);
            }
            else
            {
                friends[i].setVisible(false);
            }

            // Add the label to the frame.
            frame.add(friends[i]);
        }

        // Add elements to the frame.
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setLayout(new FlowLayout());
        frame.add(button);
        frame.setSize(350, 120);
        frame.setVisible(true);

        // Add an event listener to the button.
        button.addActionListener(new ActionListener()
        {
            /**
             * Shows or hides the name label based on the current index.
             */
            @Override
            public void actionPerformed(ActionEvent e)
            {
                int length = friends.length;
                int i;

                ++currentIndex;
                if (currentIndex >= length) {
                    currentIndex = 0;
                }

                for (i = 0; i < length; ++i)
                {
                    if (i == currentIndex)
                    {
                        friends[i].setVisible(true);
                    }
                    else
                    {
                        friends[i].setVisible(false);
                    }
                }
            }
        });
    }
}

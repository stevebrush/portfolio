/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 14.
 * Chapter 15, Exercise # 10.
 * Description:
 * Create a JFrame for the Summervale Resort. Allow the user to view information
 * about different rooms available, dining options, and activities offered.
 * Include at least two options in each menu, and display the appropriate
 * information when the user makes a choice.
 */
import javax.swing.*;
import javax.swing.border.*;
import java.util.*;
import java.awt.*;
import java.awt.event.*;
public class SummervaleResort extends JFrame implements ActionListener
{
    private JPanel wrapper = new JPanel();
    private JLabel label = new JLabel("");
    private JMenuBar mainBar = new JMenuBar();
    private JMenu menu1 = new JMenu("Rooms");
    private JMenu menu2 = new JMenu("Dining");
    private JMenu menu3 = new JMenu("Activities");
    private JMenuItem room1 = new JMenuItem("Dynasty Room");
    private JMenuItem room2 = new JMenuItem("Submarine Room");
    private JMenuItem dining1 = new JMenuItem("Seafood Supreme");
    private JMenuItem dining2 = new JMenuItem("American Classic");
    private JMenuItem activity1 = new JMenuItem("Horseback Riding");
    private JMenuItem activity2 = new JMenuItem("Underwater Explorers");

    /**
     * Constructor.
     */
    public SummervaleResort()
    {
        // Configure the JFrame.
        super("Summervale Resort");
        setSize(700, 200);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        // Wrapper.
        wrapper.setLayout(new BorderLayout());
        wrapper.setBorder(new EmptyBorder(15, 15, 15, 15));
        wrapper.add(label, BorderLayout.CENTER);

        setJMenuBar(mainBar);
        mainBar.add(menu1);
        mainBar.add(menu2);
        mainBar.add(menu3);
        menu1.add(room1);
        menu1.add(room2);
        room1.addActionListener(this);
        room2.addActionListener(this);
        menu2.add(dining1);
        menu2.add(dining2);
        dining1.addActionListener(this);
        dining2.addActionListener(this);
        menu3.add(activity1);
        menu3.add(activity2);
        activity1.addActionListener(this);
        activity2.addActionListener(this);


        // Show the JFrame.
        add(wrapper);
        setVisible(true);
    }



    @Override
    /**
     * Collects the chosen information and displays it to the user.
     */
    public void actionPerformed(ActionEvent e)
    {
        Object source = e.getSource();
        if (source == room1)
        {
            label.setText("The Dynasty Room is styled like an ancient Samarai's chambers.");
        }
        else if (source == room2)
        {
            label.setText("The Submarine Room is styled like the inside of an old WW2 submarine.");
        }
        else if (source == dining1)
        {
            label.setText("The Seafood Supreme platter includes your favorite catch of the day, and mussels to share.");
        }
        else if (source == dining2)
        {
            label.setText("The American Classic dinner features a standard hamburger and fries.");
        }
        else if (source == activity1)
        {
            label.setText("Do you like horses and vast countryside? Then you'll enjoy our horseback riding package!");
        }
        else if (source == activity2)
        {
            label.setText("The Underwater Explorers program teaches amateur divers the basics of deep-sea diving and safety.");
        }

    }

    /**
     * Init.
     */
    public static void main(String[] args)
    {
        SummervaleResort frame = new SummervaleResort();
    }
}

/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July5.
 * Chapter 14, Exercise # 7.
 * The JPopulation application displays the population of a selected city.
 */
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
public class JPopulation
{
    private static JComboBox<String> select;
    private static String[] population;
    private static JLabel label;
    public static void main(String[] args)
    {
        JFrame frame = new JFrame("City Population");
        String[] cities = {"Denver", "Cincinnati", "St. Louis", "San Diego", "New Orleans", "New York City", "Austin"};

        // Create the frame elements.
        select = new JComboBox<String>(cities);
        label = new JLabel("");
        String[] temp = {"649,495", "297,517", "318,416", "3,211,000", "378,715", "8,406,000", "885,400"};
        population = temp;

        // Add elements to the frame.
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setLayout(new FlowLayout());
        frame.add(select);
        frame.add(label);
        frame.setSize(350, 120);
        frame.setVisible(true);

        // Event listener for combo box.
        select.setSelectedIndex(1);
        select.addItemListener(new ItemListener()
        {
            @Override
            public void itemStateChanged(ItemEvent e)
            {
                int index = select.getSelectedIndex();
                label.setText("Population: " + population[index]);
            }
        });
        select.setSelectedIndex(0);
    }
}

/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July5.
 * Chapter 14, Exercise # 10.
 * The JPhotoFrame application calculates the cost of a photo session, based on
 * settings selected by the user.
 */
import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
public class JPhotoFrame
{
    public static void main(String[] args)
    {
        /**
         * $40 = one person, base price
         * $75 = fee for two or more people
         * $95 = fee for a pet
         * add $90 for on-site shoots
         */

        final int BASE_PRICE = 40;
        final int MULTIPLE_FEE = 75;
        final int PET_FEE = 95;
        final int ON_SITE_FEE = 90;

        JFrame frame = new JFrame("Photography Session Estimate Calculator");

        // Subjects.
        JLabel subjectsLabel = new JLabel("How many subjects?");
        ButtonGroup subjects = new ButtonGroup();
        JCheckBox onePerson = new JCheckBox("One person ($" + BASE_PRICE + ")", true);
        JCheckBox twoPersons = new JCheckBox("Two or more persons (add $" + MULTIPLE_FEE + ")");
        JCheckBox pet = new JCheckBox("Pet (add $" + PET_FEE + ")");
        subjects.add(onePerson);
        subjects.add(twoPersons);
        subjects.add(pet);

        // Location.
        JLabel locationLabel = new JLabel("Where will the session occur?");
        ButtonGroup location = new ButtonGroup();
        JCheckBox atStudio = new JCheckBox("Paula's Portrait Studio", true);
        JCheckBox onsite = new JCheckBox("On-location (add $" + ON_SITE_FEE + ")");
        location.add(atStudio);
        location.add(onsite);

        // The price label and button!
        JLabel priceLabel = new JLabel("The price for your session is:");
        JTextField price = new JTextField(15);
        price.setEditable(false);
        JButton button = new JButton("Calculate!");

        // Add components to frame.
        frame.add(subjectsLabel);
        frame.add(onePerson);
        frame.add(twoPersons);
        frame.add(pet);
        frame.add(locationLabel);
        frame.add(atStudio);
        frame.add(onsite);
        frame.add(button);
        frame.add(priceLabel);
        frame.add(price);
        frame.setSize(250, 300);
        frame.setLayout(new FlowLayout());
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setVisible(true);

        button.addActionListener(new ActionListener()
        {
            /**
             * Calculates the total cost of the session.
             */
            @Override
            public void actionPerformed(ActionEvent e)
            {
                int total = 0;
                if (twoPersons.isSelected())
                {
                    total = BASE_PRICE + MULTIPLE_FEE;
                }
                else if (pet.isSelected())
                {
                    total = BASE_PRICE + PET_FEE;
                }
                else
                {
                    total = BASE_PRICE;
                }
                if (onsite.isSelected())
                {
                    total += ON_SITE_FEE;
                }
                price.setText("$" + total);
            }
        });
    }
}

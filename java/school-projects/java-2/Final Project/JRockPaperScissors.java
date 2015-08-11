/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 28.
 * Final Project: Chapter 17, Game Zone # 2, Page 989.
 *
 * Description:
 * ------------
 * Create a Rock/Paper/Scissors game using a JApplet which the user can click one
 * of the three buttons labeled "Rock", "Paper", or "Scissors". The computer's
 * choice is still randomly generated. Keep track of the results after each round.
 *
 * Additional Requirements:
 * ------------------------
 * Keep a tally that shows the number of games that the player has won, tied, or lost.
 * Use the drawString() method to display your name, course, section number, and MEID in the
 * lower left-hand corner of the applet.
 */
import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import javax.swing.border.*;
import java.util.Random;

public class JRockPaperScissors extends JApplet implements ActionListener
{
    private JPanel wrapper = new JPanel();
    private JPanel buttonPanel = new JPanel();
    private JPanel statsPanel = new JPanel();
    private JLabel stats = new JLabel();
    private JButton rock = new JButton("Rock");
    private JButton paper = new JButton("Paper");
    private JButton scissors = new JButton("Scissors");
    private int wins = 0;
    private int losses = 0;
    private int ties = 0;
    private String message = "";
    private String[] outcomes = {"rock", "paper", "scissors"};

    public JRockPaperScissors()
    {
        //super("Rock Paper Scissors");
        //setSize(330, 300);
        //setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        JLabel credits = new JLabel("<html><body></body></html>");

        rock.setPreferredSize(new Dimension(100, 40));
        paper.setPreferredSize(new Dimension(100, 40));
        scissors.setPreferredSize(new Dimension(100, 40));

        rock.addActionListener(this);
        paper.addActionListener(this);
        scissors.addActionListener(this);

        buttonPanel.setLayout(new BorderLayout());
        buttonPanel.add(rock, BorderLayout.WEST);
        buttonPanel.add(paper, BorderLayout.CENTER);
        buttonPanel.add(scissors, BorderLayout.EAST);
        buttonPanel.setPreferredSize(new Dimension(300, 100));

        statsPanel.setLayout(new BorderLayout());
        statsPanel.add(stats, BorderLayout.CENTER);
        statsPanel.add(credits, BorderLayout.SOUTH);
        statsPanel.setPreferredSize(new Dimension(300, 100));
        statsPanel.setBorder(new EmptyBorder(0,15,0, 0));

        wrapper.setLayout(new BorderLayout());
        wrapper.setBorder(new EmptyBorder(15, 15, 45, 15));

        wrapper.add(buttonPanel, BorderLayout.CENTER);
        wrapper.add(statsPanel, BorderLayout.EAST);

        updateScore();

        add(wrapper);
        setVisible(true);
    }

    private void updateScore()
    {
        if (message.equals(""))
        {
            message = "Make your selection...";
        }
        stats.setText("<html><body><strong>" + message + "</strong><br><br><strong>Game Stats:</strong><br><hr>Wins: " + wins + "<br>Losses: " + losses + "<br>Ties: " + ties + "</body></html>");
    }

    @Override
    public void paint(Graphics g)
    {
        super.paint(g);
        g.drawString("Steve Brush - CIS263AA - Class # 13704 - MEID: STE2253193", 15, 155);
    }

    @Override
    public void actionPerformed(ActionEvent e)
    {
        Random random = new Random();
        int index = random.nextInt(outcomes.length);
        String choice = outcomes[index];
        JButton button = (JButton)e.getSource();

        if ((button == rock && choice == "paper") ||
            (button == paper && choice == "scissors") ||
            (button == scissors && choice == "rock"))
        {
            ++losses;
            message = button.getText() + " vs. " + choice + "... You lose :(";
        }
        else if (button.getText().toLowerCase().equals(choice))
        {
            ++ties;
            message = button.getText() + " vs. " + choice + "... It's a tie.";
        }
        else
        {
            ++wins;
            message = button.getText() + " vs. " + choice + "... You win!";
        }
        updateScore();
    }

    public void init()
    {
        JRockPaperScissors applet = new JRockPaperScissors();
    }
}

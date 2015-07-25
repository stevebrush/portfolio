/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 3.
 * The DoublesTennisGame class keeps track of two tennis players, their
 * partners, and their final scores.
 */
public class DoublesTennisGame extends TennisGame
{
    private String player1PartnerName;
    private String player2PartnerName;
    /**
     * Getters and Setters.
     */
    public String getPlayer1PartnerName()
    {
        return player1PartnerName;
    }
    public String getPlayer2PartnerName()
    {
        return player2PartnerName;
    }

    /**
     * Sets the names of the players and their partners.
     */
    public void setNames(String name1, String name1Partner, String name2, String name2Partner)
    {
        player1Name = name1;
        player2Name = name2;
        player1PartnerName = name1Partner;
        player2PartnerName = name2Partner;
    }

    /**
     * Displays the final score and names for all 4 players.
     */
    public void displayResults()
    {
        System.out.println(getPlayer1Name() + " and " + getPlayer1PartnerName() + " vs. " + getPlayer2Name() + " and " + getPlayer2PartnerName() + " - Final Score (" + getPlayer1Score() + " - " + getPlayer2Score() + "): " + getPlayer1FormattedScore() + " / " + getPlayer2FormattedScore());
    }
}

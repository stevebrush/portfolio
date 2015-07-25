/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 3.
 * The TennisGame class keeps track of two tennis players and their final scores.
 */
public class TennisGame
{
    protected String player1Name;
    protected String player2Name;
    private int player1Score;
    private int player2Score;
    private String player1FormattedScore;
    private String player2FormattedScore;

    /**
     * Getters and Setters
     */
    public String getPlayer1Name()
    {
        return player1Name;
    }
    public String getPlayer2Name()
    {
        return player2Name;
    }
    public int getPlayer1Score()
    {
        return player1Score;
    }
    public int getPlayer2Score()
    {
        return player2Score;
    }
    public String getPlayer1FormattedScore()
    {
        return player1FormattedScore;
    }
    public String getPlayer2FormattedScore()
    {
        return player2FormattedScore;
    }

    /**
     * Sets the names of the two tennis players.
     */
    public void setNames(String name1, String name2)
    {
        player1Name = name1;
        player2Name = name2;
    }

    /**
     * Sets and calculates the final scores for the players.
     */
    public void setScores(int score1, int score2)
    {
        if (score1 < 0 || score1 > 4 || score2 < 0 || score2 > 4 || (score1 == 4 && score2 == 4)) {
            score1 = 0;
            score2 = 0;
            player1FormattedScore = "error";
            player2FormattedScore = "error";
        } else {
            player1FormattedScore = formatScore(score1);
            player2FormattedScore = formatScore(score2);
        }
        player1Score = score1;
        player2Score = score2;
    }

    /**
     * Returns the human-readable version of the tennis scores.
     */
    private String formatScore(int score)
    {
        String formattedScore = "";
        switch (score) {
            case 0:
            formattedScore = "love";
            break;
            case 1:
            formattedScore = "15";
            break;
            case 2:
            formattedScore = "30";
            break;
            case 3:
            formattedScore = "40";
            break;
            case 4:
            formattedScore = "game";
            break;
        }
        return formattedScore;
    }

    /**
     * Displays all fields associated with this tennis game.
     */
    public void displayResults()
    {
        System.out.println(getPlayer1Name() + " vs. " + getPlayer2Name() + " - Final Score (" + getPlayer1Score() + " - " + getPlayer2Score() + "): " + getPlayer1FormattedScore() + " / " + getPlayer2FormattedScore());
    }
}

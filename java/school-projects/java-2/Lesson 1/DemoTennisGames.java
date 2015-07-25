/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 3.
 * The DemoTennisGames application demonstrates the various fields and behaviors
 * of the TennisGame and DoublesTennisGame classes.
 */
public class DemoTennisGames
{
    public static void main(String args[])
    {
        TennisGame game1 = new TennisGame();
        game1.setNames("Jonny", "James");
        game1.setScores(2, 1);
        game1.displayResults();

        TennisGame game2 = new TennisGame();
        game2.setNames("Phyllis", "Deborah");
        game2.setScores(5, 3);
        game2.displayResults();

        TennisGame game3 = new TennisGame();
        game1.setNames("Dillan", "Qua'jon");
        game1.setScores(0, 4);
        game1.displayResults();

        DoublesTennisGame doublesGame1 = new DoublesTennisGame();
        doublesGame1.setNames("Sam", "Greg", "Phillip", "Peter");
        doublesGame1.setScores(0, 1);
        doublesGame1.displayResults();

        DoublesTennisGame doublesGame2 = new DoublesTennisGame();
        doublesGame2.setNames("Shawna", "Lucy", "Mandy", "Bethany");
        doublesGame2.setScores(4, 3);
        doublesGame2.displayResults();

        DoublesTennisGame doublesGame3 = new DoublesTennisGame();
        doublesGame3.setNames("Liza", "Jim", "Lennie", "Chris");
        doublesGame3.setScores(-1, 2);
        doublesGame3.displayResults();
    }
}

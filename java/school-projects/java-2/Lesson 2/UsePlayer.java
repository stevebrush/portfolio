/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 12.
 * The UsePlayer application demonstrates the use of various Player objects.
 */
public class UsePlayer
{
    public static void main(String[] args) {
        Child child = new Child();
        child.play();

        Actor actor = new Actor();
        actor.play();

        Musician musician = new Musician();
        musician.play();
    }
}

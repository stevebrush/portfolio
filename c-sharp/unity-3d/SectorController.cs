using UnityEngine;
using System.Collections;

public class SectorController : MonoBehaviour {

	private SpriteRenderer sprite;
	private FadeInOut fadeInOut;

	// Use this for initialization
	void Start () {
		sprite = GetComponent<SpriteRenderer> ();
		sprite.sortingOrder = 0;
		fadeInOut = GetComponent<FadeInOut> ();
		fadeInOut.SetOpacity (0.17f);
	}
}

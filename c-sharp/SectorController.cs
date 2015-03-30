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
		/*
		Color color = sprite.color;
		color.a = 0.0f;
		sprite.color = color;
		*/
		fadeInOut.SetOpacity (0.17f);
	}
}

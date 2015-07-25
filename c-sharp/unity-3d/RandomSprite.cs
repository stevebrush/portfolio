using UnityEngine;
using System.Collections;

public class RandomSprite : MonoBehaviour {

	public Sprite[] sprites;
	public string resourceName;

	// Use this for initialization
	void Start () {
		if (resourceName != "") {
			sprites = Resources.LoadAll<Sprite> (resourceName);
			GetComponent<SpriteRenderer>().sprite = sprites[Random.Range (0, sprites.Length)];
		}
	}
	
	// Update is called once per frame
	void Update () {
	
	}
}

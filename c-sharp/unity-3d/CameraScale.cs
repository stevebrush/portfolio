using UnityEngine;
using System.Collections;

public class CameraScale : MonoBehaviour {

	void Awake() {
		camera.orthographicSize = (Screen.height / 100f / 2f);
		// 100f = pixel ratio set up in debug
	}

	// Use this for initialization
	void Start () {

	}
	
	// Update is called once per frame
	void Update () {

	}
}

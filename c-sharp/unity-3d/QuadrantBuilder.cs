using UnityEngine;
using System.Collections;

public class QuadrantBuilder : MonoBehaviour {

	public Camera mainCamera;
	public GameObject quadrant;
	public int numQuadrants = 4;

	public GameObject[] BuildQuadrants() {

		GameObject[] quadrants = new GameObject[numQuadrants];
		GameObject newQuadrant;
		QuadrantController newQuadrantScript;
		
		for (int i = 0; i < numQuadrants; i++) {
			newQuadrant = (GameObject)GameObject.Instantiate(quadrant);
			newQuadrantScript = newQuadrant.GetComponent<QuadrantController>();
			newQuadrantScript.placement = i;
			newQuadrantScript.mainCamera = mainCamera;
			quadrants[i] = newQuadrant;
		}
		return quadrants;
	}
}

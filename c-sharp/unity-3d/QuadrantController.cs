using UnityEngine;
using System.Collections;

public class QuadrantController : MonoBehaviour {

	public Camera mainCamera;
	public int placement;

	private int[] coordinates = new int[2];
	private float scaleX;
	private float scaleY;

	// Use this for initialization
	void Start () {

		// Determine which section of the graph the quadrant should appear.
		switch (placement) {
		case 0:
			coordinates[0] = -1;
			coordinates[1] = 1;
			break;
		case 1:
			coordinates[0] = 1;
			coordinates[1] = 1;
			break;
		case 2:
			coordinates[0] = 1;
			coordinates[1] = -1;
			break;
		case 3:
			coordinates[0] = -1;
			coordinates[1] = -1;
			break;
		}

		float width = ((Screen.width / 2f) - 19f - 6f) / 2f;
		scaleX = ((width + 6f) / 100f) * coordinates [0];
		
		float height = ((Screen.height / 2f) - 44f - 105f) / 2f;
		scaleY = ((height + 105f) / 100f) * coordinates [1];

		transform.parent = mainCamera.transform;
		transform.localPosition = new Vector3 (scaleX, scaleY, 2f);
	}
}

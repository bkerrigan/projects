#include <stdio.h>
#include <stdlib.h>

/* find_max_n
 *
 * Finds the maximum value of a contigious sub-array
 * Runs in O(n) time for n being the size of the array
 * 
 * @param: array - pointer to the beginning of the array
 * @param: array_size - number of array elements
 * @return: int - the maximum contigious sub-array value.
 * If all the array elements are negative, zero will be returned
 */
int find_max_n(int *array, uint array_size)
{
    int max = 0;    //The maximum sub-array seen so far
    int sum = 0;    //The maximum sub-array ending at position i
    int i;

    if (array == NULL || array_size == 0) {
	printf("\nInvalid array input");
	return 0;
    }

    for (i=0; i < array_size; i++) {
        /* Sum the largest sub-array ending at the current location */
	if ((sum+array[i]) > 0) {
	    sum += array[i];
	} else {
	    /* if the current sum is negative, start again at zero */
	    sum = 0;
	}

	/* Check and see if sum is larger than the maximum sub-array
	 * seen so far
	 */
	if (sum > max) {
	    max = sum;
	}
    }
    return max;
}

/* find_max_n2
 *
 * Finds the maximum value of a contigious sub-array
 * Runs in O(n^2) time for n being the size of the array
 * 
 * @param: array - pointer to the beginning of the array
 * @param: array_size - number of array elements
 * @return: int - the maximum, contigious sub-array value.
 * If all the array elements are negative, zero will be returned
 */
int find_max_n2(int *array, uint array_size)
{
    int max = 0;
    int i,j;

    if (array == NULL || array_size == 0) {
	printf("\nInvalid array input");
	return 0;
    }

    for (i=0; i < array_size; i++) {
	int sum = 0;	//Sum from i to the end of array
	for (j=i; j < array_size; j++) {
	    sum += array[j];

	    /* If the subarray sum is larger than max
	     * set max to the current sum and continue
	     */
	    if (sum > max) {
		max = sum;
	    }
	}
    }
    return max;
}

/* find_max_n3
 *
 * Finds the maximum value of a contigious sub-array
 * Runs in O(n^3) time for n being the size of the array
 * 
 * @param: array - pointer to the beginning of the array
 * @param: array_size - number of array elements
 * @return: int - the maximum contigious sub-array value.
 * If all the array elements are negative, zero will be returned
 */
int find_max_n3(int *array, uint array_size)
{
    int max = 0;
    int i,j,k;

    if (array == NULL || array_size == 0) {
	printf("\nInvalid array input");
	return 0;
    }

    for (i=0; i < array_size; i++) {
	for (j=i; j < array_size; j++) {
	    int sum = 0; //value of array[i]+...+array[j]
	    for (k=i; k<=j; k++) {
	        sum += array[k];
	    }
	    /* If the subarray sum is larger than max
	     * set max to the current sum and continue
	     */
	    if (sum > max) {
		max = sum;
	    }
	}
    }
    return max;
}


int main(int argc, char **argv) {
    int array[10] = {10,-1,2,3,4,5,6,7,8,9}; //change these values to test
    int max = 0;

    /* Call each function to find the max value, should be the same */
    max = find_max_n(array, (sizeof(array)/sizeof(int)));
    printf("\nThe max value in the array from find_max_n is %d", max);
    max = find_max_n2(array, (sizeof(array)/sizeof(int)));
    printf("\nThe max value in the array from find_max_n2 is %d", max);
    max = find_max_n3(array, (sizeof(array)/sizeof(int)));
    printf("\nThe max value in the array from find_max_n3 is %d\n", max);

    return 0;
}

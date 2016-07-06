#include <stdio.h>
#include <stdlib.h>
#include <malloc.h>

#define MAX_QUEUE_SIZE 50
#define MIN_QUEUE_SIZE 1

/* Queue element */
struct Element {
    int value;
};

/* Queue buffer object */
struct BoundedQueue {
    int start;                  // oldest element
    int size;                   // size of buffer
    int count;                  // number of elements
    struct Element *elements;   // buffer elements
};

/* Initialize the queue
 *
 * @param queue - pointer to the queue
 * @param size  - size of the queue
 * @return      - 1 on success, 0 on error
 */
static int queue_init (struct BoundedQueue *queue, int size) {
    queue->start = 0;
    queue->count = 0;

    if ((size < MIN_QUEUE_SIZE) || (size > MAX_QUEUE_SIZE) ) {
        printf("Invalid queue size. Please specify a value between %d and %d\n", MIN_QUEUE_SIZE, MAX_QUEUE_SIZE);
        return 0;
    }
    queue->size = size;

    queue->elements = calloc(queue->size, sizeof(struct Element));
    if (queue->elements == NULL) {
        printf("Unable to allocate queue buffer\n");
        return 0;
    }
    return 1;
}

/* Free the queue memory
 *
 * @param pointer to the queue
 */
static void queue_free (struct BoundedQueue *queue) {
    free(queue->elements);
}

/* Check if the queue is empty
 *
 * @param queue - pointer to the queue
 * @return      - 1 if empty, 0 otherwise
 */
static int is_queue_empty (struct BoundedQueue *queue) {
    if (queue == NULL) {
        printf("Invalid queue in function is_queue_empty()\n");
        return 0;
    }
    return (queue->count == 0);
}

/* Check if the queue is full
 *
 * @param queue - pointer to the queue
 * @return      - 1 if full, 0 otherwise
 */
static int is_queue_full (struct BoundedQueue *queue) {
    if (queue == NULL) {
        printf("Invalid queue in function is_queue_full()\n");
        return 0;
    }
    return (queue->size == queue->count);
}

/* Add an element to the queue
 * NOTE: Will overwrite the last element if the queue is full
 *
 * @param queue   - pointer to the queue
 * @param element - element to enqueue
 */
static void enqueue (struct BoundedQueue *queue, struct Element *element) {
    if (queue == NULL) {
        printf("Invalid queue in function enqueue()\n");
        return;
    }
    int end = (queue->start + queue->count) % queue->size;
    queue->elements[end] = *element;
    struct Element e = queue->elements[end];
    if (is_queue_full(queue)) {
        queue->start = (queue->start + 1) % queue->size;
    } else {
        queue->count++;
    }
}

/* Remove an element from the queue
 *
 * @param queue   - pointer to the queue
 * @param element - element being dequeue
 */
static void dequeue (struct BoundedQueue *queue, struct Element *element) {
    if (queue == NULL) {
        printf("Invalid queue in function dequeue()\n");
        return;
    }
    if (!is_queue_empty(queue)) {
        *element = (struct Element)(queue->elements[queue->start]);
        queue->start = (queue->start + 1) % queue->size;
        queue->count--;
    }
}

/*******************
 * Testing functions
 ******************/

/* Print the contents of the queue
 * NOTE: Does not modify the queue
 *
 * @param queue   - pointer to the queue
 */
static void print_queue (struct BoundedQueue *queue) {
    if (queue == NULL) {
        printf("Invalid queue in function dequeue()\n");
        return;
    }
    printf("Queue contents are (oldest to newest):\n");
    int i = queue->count;
    int index = queue->start;
    for (i; i > 0; i--) {
        struct Element e = queue->elements[index];
        printf("%d", e.value);
        if (i > 1) printf(",");
        index = (index + 1) % queue->size;
    }
    printf("\n");
}

/* Tests the queue_init function and prints the result
 * Any queue allocated by the call to queue_init will be freed
 *
 * @param queue - pointer to the queue
 * @param size  - size of the queue
 * @return      - 1 on success, 0 on error
 */
static int test_queue_init (struct BoundedQueue *queue, int test_size) {
    printf("Testing queue_init with size %d\n", test_size);
    if (queue_init(queue, test_size)) {
        printf("Successfully initialized queue with size %d\n", test_size);
        queue_free (queue);
        return 1;
    } else {
        printf("Error initializing queue with size %d\n", test_size);
        return 0;
    }
}

int main (int argc, char **argv) {
    struct BoundedQueue q;
    struct Element e;
    int size = MAX_QUEUE_SIZE/2;

    // Test the bounded queue
    int num_success = 0;
    int num_failure = 0;

    /* Test the queue init */
    // Positive test cases
    printf("\nTesting the queue_init function\n\n");
    int result = test_queue_init(&q, 10);
    if (result == 1) { 
        num_success++; 
    } else { 
        num_failure++; 
    }

    result = test_queue_init(&q, MAX_QUEUE_SIZE);
    if (result == 1) { 
        num_success++; 
    } else { 
        num_failure++;
    }

    // Negative test cases
    result = test_queue_init(&q, MAX_QUEUE_SIZE+1);
    if (result == 1) { 
        num_failure++; 
    } else { 
        num_success++;
    }

    result = test_queue_init(&q, 0);
    if (result == 1) { 
        num_failure++;
    } else { 
        num_success++;
    }

    result = test_queue_init(&q, -1);
    if (result == 1) { 
        num_failure++; 
    } else { 
        num_success++;
    }
    printf("\nFinished testing queue_init. %d tests passed and %d tests failed\n\n", num_success, num_failure);

    // Initialize the queue
    queue_init(&q, size);

    // Test is_queue_empty, is_queue_full, and dequeue on empty queue
    num_success = 0;
    num_failure = 0;
    result = is_queue_empty(&q);
    if (result == 1) {
        num_success++;
    } else {
        num_failure++;
    }
    result = is_queue_full(&q);
    if (result == 1) {
        num_failure++;
    } else {
        num_success++;
    }
    e.value = 30;
    dequeue(&q, &e);    // dequeue shouldn't do anything if the queue is empty so the value shoudl remain unchanged
    if (e.value == 30) {
        num_success++;
    } else {
        num_failure++;
    }
    printf("Testing is_queue_empty and is_queue_full on empty queue. %d tests passed and %d tests failed\n\n", num_success, num_failure);

    // Fill the queue
    printf("Filling the queue. Size is %d\n", size);
    int i = 0;
    for (i; i < size; i++) {
        e.value = i;
        enqueue(&q, &e);
    }
    
    // Show the queue contents
    print_queue(&q); 

    // Test is_queue_empty and is_queue_full on empty queue
    num_success = 0;
    num_failure = 0;
    //Positive test case
    result = is_queue_full(&q);
    if (result == 1) {
        num_success++;
    } else {
        num_failure++;
    }
    //Negative test case
    result = is_queue_empty(&q);
    if (result == 1) {
        num_failure++;
    } else {
        num_success++;
    }
    printf("\nTesting is_queue_empty and is_queue_full on full queue. %d tests passed and %d tests failed\n\n", num_success, num_failure);

    // Test dequeue function, expect to get back the first value of 0
    printf("Testing the dequeue function on full queue\n\n");
    num_success = 0;
    num_failure = 0;
    //Positive test case
    dequeue(&q, &e);
    if (e.value == 0) {
        num_success++;
    } else {
        num_failure++;
    }
    printf("Finished testing dequeue. %d tests passed and %d tests failed\n\n", num_success, num_failure);

    // Show the queue contents
    print_queue(&q); 

    // Enqueue two items to fill the queue and test the wrap around
    printf("\nFilling the queue again\n");
    e.value = size;
    enqueue(&q, &e);

    if (is_queue_full(&q)) {
        printf("Queue is full!\n");
    } else {
        printf("Error: queue is suppose to be full but is not\n");
    }
    // Show the queue contents
    print_queue(&q); 


    printf("\nTesting enqueue on a full queue. Should overwrite the oldest value\n");
    e.value = size+1;
    enqueue(&q, &e);

    // Show the queue contents
    print_queue(&q); 

    // Free the queue
    queue_free (&q);
}

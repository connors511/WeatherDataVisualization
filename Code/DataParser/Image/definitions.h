/****************************************
* Definitions Header
****************************************/

#ifndef __DEFINITIONS_H
#define __DEFINITIONS_H

#include "types.h"


/* Needed to make functions work on ARM */
#define MAX_WIDTH 1024
#define MAX_HEIGHT 1024

/* data structure for the grayscale image, 1 BYTE / pixel */
typedef struct {
	WORD  Height;
	WORD  Width;
	BYTE  Pixels[MAX_WIDTH * MAX_HEIGHT];	
} IMAGE;

#endif

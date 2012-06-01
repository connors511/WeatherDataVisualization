#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>

#include "bmp.h"

/* see lecture notes for more information on pragma pack directive */
#pragma pack(push, 1)

/*
*   BITMAP FILE: See http://en.wikipedia.org/wiki/BMP_file_format
*   ___________________________________________________________
*  |          |          |            |                        |
*  |   file   |   info   |  Palette   |       Pixel data       |     
*  |  header  |  header  | (optional) |                        |
*  |__________|__________|____________|________________________|
*  start of file 									  end of file
* 
*  - Lines must be word-aligned!
*  
* 
*/

/**********************************************************************
* Bitmap File Header
**********************************************************************/
typedef struct {
	HALFWORD BfType; /* Must be 0x4D42 				  */
	WORD BfSize; /* Size of the file in bytes 	  */
	HALFWORD BfReserved1; /* Should be 0 					  */
	HALFWORD BfReserved2; /* Should be 0 					  */
	WORD BfOffBits; /* Offset of image data in file   */
} BITMAPFILEHEADER;

/**********************************************************************
* Bitmap Information Header
**********************************************************************/
typedef struct {
	WORD BiSize; /* Size of this structure 		  */
	WORD BiWidth; /* Width of the image in bytes    */
	WORD BiHeight; /* Height of the image in bytes   */
	HALFWORD BiPlanes; /* Should be 1 					  */
	HALFWORD BiBitCount; /* Bit count (..) 				  */
	WORD BiCompression; /* Compression used 			  */
	WORD BiSizeImage; /* Size of the image in bytes 	  */
	WORD BiXPelsPerMeter; /* Pixels per meter, X			  */
	WORD BiYPelsPerMeter; /* Pixels per meter, Y 			  */
	WORD BiClrUsed; /* number of colors used 		  */
	WORD BiClrImportant; /* number of important colors 	  */
} BITMAPINFOHEADER;

/**********************************************************************
* Color palette entry
**********************************************************************/
typedef struct {
	BYTE Blue;
	BYTE Green;
	BYTE Red;
	BYTE Alpha;
} COLORENTRY;

/* add here other structs you want to pack */

#pragma pack(pop)

typedef enum {
	BI_RGB = 0,
	BI_RLE8,
	BI_RLE4,
	BI_BITFIELDS, //Also Huffman 1D compression for BITMAPCOREHEADER2
	BI_JPEG, //Also RLE-24 compression for BITMAPCOREHEADER2
        BI_PNG
} BMPCOMPRESSIONMETHOD;

int bmp_open(char* file, IMAGE* image) {

	BITMAPFILEHEADER bmfh;
	BITMAPINFOHEADER bmih;
        unsigned int i;
	BYTE blue, green, red, padding;

	/* note: "rb" means open for binary read */
	FILE* fp = fopen(file, "rb");

	if (fp == NULL) {
                /* failed to open file, return failure */
                perror("Could not open file");
                return 0;
	}


	/* todo: process file */
	fread(&bmfh, sizeof (bmfh), 1, fp);
	fread(&bmih, sizeof (bmih), 1, fp);

	image->Height = bmih.BiHeight;
	image->Width = bmih.BiWidth;

	// Starting address of the image data can be found in the file header
	fseek(fp, bmfh.BfOffBits, SEEK_SET);
	if (bmih.BiCompression == BI_RGB) {

		if (bmih.BiBitCount > 8) {
			padding = sizeof (BYTE) * ((image->Width) % 4);
			for (i = 0; i < (bmih.BiWidth * bmih.BiHeight); i++) {
				fread(&blue, sizeof (BYTE), 1, fp);
				fread(&green, sizeof (BYTE), 1, fp);
				fread(&red, sizeof (BYTE), 1, fp);
				image->Pixels[i] = 0.11 * blue + 0.59 * green + 0.3 * red;
				if ((i + 1) % image->Width == 0) {
					// DWORD-aligned padding
					fseek(fp, padding, SEEK_CUR);
				}
			}
		} else {
			// Assuming 8 bit
			padding = 4 - ((image->Width) % 4);
			padding = padding == 4 ? 0 : padding * sizeof (BYTE);
			for (i = 0; i < (bmih.BiWidth * bmih.BiHeight); i++) {
				fread(&blue, sizeof (BYTE), 1, fp);
				image->Pixels[i] = blue;
				if ((i + 1) % (4 - image->Width) == 0) {
					// DWORD-aligned padding
					fseek(fp, padding, SEEK_CUR);
				}
			}
		}

	} else if (bmih.BiCompression == BI_RLE8) {
                return 0;
	}


	/* success */
	fclose(fp);
        return 1;
}

int bmp_save(char* file, IMAGE* image) {

	BITMAPFILEHEADER bmfh;
	BITMAPINFOHEADER bmih;
        unsigned int i;
        BYTE reserved = 0, padding;

	/* note: "wb" means open for binary write */
	FILE* fp = fopen(file, "wb");

	if (fp == NULL) {
		/* failed to open file, return failure */
		perror("Could not open file");
                return 0;
	}

	/* todo: store image to fp */
	bmih.BiSize = sizeof (BITMAPINFOHEADER);
	bmih.BiWidth = image->Width;
	bmih.BiHeight = image->Height;
	bmih.BiPlanes = 1;
	bmih.BiBitCount = 8;
	bmih.BiCompression = BI_RGB;
	bmih.BiSizeImage = sizeof (BYTE) * image->Width * image->Height;
	bmih.BiXPelsPerMeter = 0; //2834;
	bmih.BiYPelsPerMeter = 0; //2834;
	bmih.BiClrUsed = 256;
	bmih.BiClrImportant = 256;

	bmfh.BfType = 0x4D42;
	bmfh.BfOffBits = sizeof (BITMAPFILEHEADER) + sizeof (BITMAPINFOHEADER) + sizeof (BYTE) * 1024;
	bmfh.BfSize = bmfh.BfOffBits + bmih.BiSizeImage;
	bmfh.BfReserved1 = 0;
	bmfh.BfReserved2 = 0;

	fwrite(&bmfh.BfType, sizeof(bmfh.BfType), 1, fp);
	fwrite(&bmfh.BfSize, sizeof(bmfh.BfSize), 1, fp);
	fwrite(&bmfh.BfReserved1, sizeof(bmfh.BfReserved1), 1, fp);
	fwrite(&bmfh.BfReserved2, sizeof(bmfh.BfReserved2), 1, fp);
	fwrite(&bmfh.BfOffBits, sizeof(bmfh.BfOffBits), 1, fp);
	
	//fwrite(&bmih, sizeof(bmih), 1, fp);
	fwrite(&bmih.BiSize, sizeof(bmih.BiSize), 1, fp);
	fwrite(&bmih.BiWidth, sizeof(bmih.BiWidth), 1, fp);
	fwrite(&bmih.BiHeight, sizeof(bmih.BiHeight), 1, fp);
	fwrite(&bmih.BiPlanes, sizeof(bmih.BiPlanes), 1, fp);
	fwrite(&bmih.BiBitCount, sizeof(bmih.BiBitCount), 1, fp);
	fwrite(&bmih.BiCompression, sizeof(bmih.BiCompression), 1, fp);
	fwrite(&bmih.BiSizeImage, sizeof(bmih.BiSizeImage), 1, fp);
	fwrite(&bmih.BiXPelsPerMeter, sizeof(bmih.BiXPelsPerMeter), 1, fp);
	fwrite(&bmih.BiYPelsPerMeter, sizeof(bmih.BiYPelsPerMeter), 1, fp);
	fwrite(&bmih.BiClrUsed, sizeof(bmih.BiClrUsed), 1, fp);
	fwrite(&bmih.BiClrImportant, sizeof(bmih.BiClrImportant), 1, fp);

	for (i = 0; i < 256; i++) {
		fwrite(&i, sizeof (BYTE), 1, fp);
		fwrite(&i, sizeof (BYTE), 1, fp);
		fwrite(&i, sizeof (BYTE), 1, fp);
		fwrite(&reserved, sizeof (BYTE), 1, fp);
	}


        padding = image->Width % 4;
        for (i = 0; i < (bmih.BiWidth * bmih.BiHeight); i++) {
                fwrite(&image->Pixels[i], sizeof (BYTE), 1, fp);
                if (((i + 1) % image->Width == 0)) {
                        fwrite(&reserved, sizeof (BYTE), padding, fp); // padding
                }

        }

	fclose(fp);
        return 1;
}

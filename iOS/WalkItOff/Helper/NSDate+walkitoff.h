//
//  NSDate+walkitoff.h
//  WalkItOff
//
//  Created by Donald Pae on 7/2/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

#define DATETIME_FORMAT     @"yyyy-MM-dd HH:mm:ss"
#define DATE_FORMAT         @"yyyy-MM-dd"

@interface NSDate (walkitoff)

- (NSComparisonResult)compareOnlyDate:(NSDate *)date;

@end

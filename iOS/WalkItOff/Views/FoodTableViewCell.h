//
//  FoodTableViewCell.h
//  WalkItOff
//
//  Created by Donald Pae on 6/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Food.h"

@interface FoodTableViewCell : UITableViewCell

@property (nonatomic, strong) Food *food;


- (void)bind:(Food *)food;

@end
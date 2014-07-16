//
//  FoodTableViewCell.h
//  WalkItOff
//
//  Created by Donald Pae on 6/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Food.h"

@protocol FoodsViewCellsDelegate;

@interface FoodTableViewCell : UITableViewCell

@property (nonatomic, strong) Food *food;
@property (nonatomic, strong) id<FoodsViewCellsDelegate> delegate;

- (CGFloat)heightForFood:(Food *)food;
- (void)bind:(Food *)food;

@end

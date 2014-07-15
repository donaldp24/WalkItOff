//
//  FoodTableViewCell.m
//  WalkItOff
//
//  Created by Donald Pae on 6/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "FoodTableViewCell.h"
#import "UIManager.h"
#import "FoodsViewController.h"

#define kTopMargin      4
#define kBottomMargin   4
#define kLeftMargin     30




@interface FoodTableViewCell ()

@property (nonatomic, strong) IBOutlet UILabel *lblName;

@end

@implementation FoodTableViewCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
    }
    return self;
}

- (void)awakeFromNib
{
    // Initialization code
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

- (void)bind:(Food *)food
{

    self.food = food;
    

}
- (void)layoutSubviews
{
    [super layoutSubviews];
    
    [self.contentView layoutSubviews];
    
    
    self.lblName.text = [NSString stringWithFormat:@"%d - %@", self.food.uid, self.food.name];
    
    CGRect rtLabel = self.lblName.frame;
    self.lblName.frame = CGRectMake(rtLabel.origin.x, 0, rtLabel.size.width, [self labelHeightForFood:self.food] + 20);
    
    self.lblName.numberOfLines = 0;
}

- (void)updateConstraints
{
    [super updateConstraints];
}

- (CGFloat)labelHeightForFood:(Food *)food
{
    CGRect labelRect = [food.name
                        boundingRectWithSize:CGSizeMake(self.lblName.frame.size.width, 300)
                        options:NSStringDrawingUsesLineFragmentOrigin
                        attributes:@{
                                     NSFontAttributeName : self.lblName.font
                                     }
                        context:nil];
    return ceilf(labelRect.size.height);

}

- (CGFloat)heightForFood:(Food *)food
{
    return [self labelHeightForFood:food] + kBottomMargin + kTopMargin + 20;
}

#pragma mark - Actions

- (IBAction)onBtnPlus:(id)sender
{

    if (self.delegate && [self.delegate respondsToSelector:@selector(onFoodCellBtnPlus:)])
        [self.delegate onFoodCellBtnPlus:self.food];


}


@end

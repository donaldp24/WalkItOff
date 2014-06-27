//
//  FavoritesTableViewCell.m
//  WalkItOff
//
//  Created by Donald Pae on 6/18/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "FavoritesTableViewCell.h"
#import "UIManager.h"

@interface FavoritesTableViewCell ()

@property (nonatomic, strong) UIView *shadeView;
@property (nonatomic, assign) BOOL didSetupConstraints;


@property (nonatomic, strong) UILabel *lblName;
@property (nonatomic, strong) UIButton *btnPlus;
@property (nonatomic, strong) UIButton *btnMinus;

@end

@implementation FavoritesTableViewCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
        self.backgroundColor = [UIColor whiteColor];
        
        self.lblName = [[UILabel alloc] initWithFrame:CGRectZero];
        self.lblName.translatesAutoresizingMaskIntoConstraints = NO;
        self.lblName.textColor = [UIManager headerTextColor];
        self.lblName.font = [UIFont systemFontOfSize:17.0];
        self.lblName.lineBreakMode = NSLineBreakByTruncatingTail;
        self.lblName.numberOfLines = 0;
        self.lblName.backgroundColor = [UIColor clearColor];
        
        self.btnPlus = [UIButton buttonWithType:UIButtonTypeCustom];
        [self.btnPlus setImage:[UIImage imageNamed:@"smallplusicon"] forState:UIControlStateNormal];
        self.btnPlus.adjustsImageWhenHighlighted = NO;
        
        self.btnMinus = [UIButton buttonWithType:UIButtonTypeCustom];
        [self.btnMinus setImage:[UIImage imageNamed:@"smallminusicon"] forState:UIControlStateNormal];
        self.btnMinus.adjustsImageWhenHighlighted = NO;
        
        [self.contentView addSubview:self.lblName];
        [self.contentView addSubview:self.btnPlus];
        [self.contentView addSubview:self.btnMinus];
 
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
    self.lblName.text = food.name;
}

- (void)updateConstraints
{
    [super updateConstraints];
    
    if (self.didSetupConstraints) return;
    
    
    /*
     CGFloat padding = 0;
     [self.btn1 mas_makeConstraints:^(MASConstraintMaker *make) {
     make.top.equalTo(self.shadeView.mas_top).offset(padding);
     make.left.equalTo(self.mas_left).offset(8.0);
     make.bottom.equalTo(self.shadeView.mas_bottom).offset(-padding);
     make.right.equalTo(self.btn2.mas_left).offset(-padding);
     make.width.equalTo(self.btn2.mas_width);
     make.height.equalTo(self.btn2.mas_height);
     }];
     
     [self.btn2 mas_makeConstraints:^(MASConstraintMaker *make) {
     make.top.equalTo(self.shadeView.mas_top).offset(padding);
     make.left.equalTo(self.btn1.mas_right).offset(padding);
     make.bottom.equalTo(self.shadeView.mas_bottom).offset(-padding);
     make.right.equalTo(self.shadeView.mas_left).offset(-padding);
     make.width.equalTo(self.btn1.mas_width);
     make.height.equalTo(self.btn1.mas_height);
     }];
     
     [self.activityCircle mas_makeConstraints:^(MASConstraintMaker *make) {
     make.size.equalTo(@12);
     make.centerY.equalTo(@0);
     make.right.equalTo(self.shadeView).with.offset(-10.0);
     }];
     */
    
    
    [self.lblName mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.contentView).offset(30);
        make.right.equalTo(self.contentView).offset(-30);
        make.top.equalTo(self.contentView).offset(4);
        make.bottom.equalTo(self.contentView).offset(-4);
    }];
    
    [self.btnMinus mas_makeConstraints:^(MASConstraintMaker *make) {
        make.width.equalTo(@(48));
        make.height.equalTo(@(48));
        make.right.equalTo(self.contentView).offset(-1);
        make.top.equalTo(self.contentView);
    }];
    
     [self.btnPlus mas_makeConstraints:^(MASConstraintMaker *make) {
         make.width.equalTo(self.btnMinus);
         make.height.equalTo(self.btnMinus);
         make.top.equalTo(self.btnMinus);
         make.right.equalTo(self.btnMinus.mas_left).offset(1);
     }];
    
    self.didSetupConstraints = YES;
    
    
}
@end

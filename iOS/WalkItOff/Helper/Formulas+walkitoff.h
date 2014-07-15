//
//  Formulas+walkitoff.h
//  WalkItOff
//
//  Created by Donald Pae on 7/3/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Formulas : NSObject


/////// home page formulas
+ (CGFloat)percentageOfCaloriesBurned:(CGFloat)caloriesRemaining totalCurrentMealsCalories:(CGFloat) totalCurrentMealsCalories;

+ (CGFloat)caloriesRemaining:(CGFloat)userCaloriesBurned totalCalories:(CGFloat)totalCurrentMealsCalories;

+ (CGFloat)distanceRemaining:(CGFloat)distanceWalked totalDistanceToWalk:(CGFloat)totalDistanceToWalk;

+ (CGFloat)stepsRemaining:(CGFloat)stepsWalked totalStepsToBeWalkedToBurnFood:(CGFloat)totalStepsToBeWalkedToBurnFood;

+ (CGFloat)estimatedTimeToWalkAtCasualPace:(CGFloat)distanceRemainingInMiles;

+ (CGFloat)estimatedTimeToWalkAtBriskPace:(CGFloat)distanceRemainingInMiles;


///////// setting page formulas
+ (CGFloat)userCaloriesBurnedPerMile:(CGFloat)userWeightInLbs;

+ (CGFloat)userStrideLengthInMiles:(CGFloat)userHeightInCm;

+ (CGFloat)userCaloriesBurnedPerStep:(CGFloat)userCaloriesburnedPerMile strideLengthInMiles:(CGFloat)strideLengthInMiles;

//////////// nutritional information page formulas
+ (CGFloat)caloriesInFood:(CGFloat)caloriesInFood;
+ (CGFloat)stepsToBeWalkedToBurnFood:(CGFloat)caloriesInFood userCaloriesBurnedPerStep:(CGFloat)userCaloriesBurnedPerStep;
+ (CGFloat)distanceToWalkToBurnFood:(CGFloat)userStrideLength stepsPerFood:(CGFloat)stepsPerFood;

///////////// other formulas
+ (CGFloat)distanceWalked:(CGFloat)userStrideLength numberOfSteps:(NSInteger)numberOfSteps;
+ (NSInteger)stepsWalked:(NSInteger)numberOfSteps;

+ (CGFloat)weightInLbsWithKg:(CGFloat)weightInKg;

@end

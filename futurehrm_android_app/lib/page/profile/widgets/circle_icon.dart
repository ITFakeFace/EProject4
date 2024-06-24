import 'package:flutter/material.dart';

class CircleIcon extends StatelessWidget {
  final IconData icon;
  final Color iconColor;
  final Color circleColor;
  final double size;

  const CircleIcon({
    Key? key,
    required this.icon,
    this.iconColor = Colors.white,
    required this.circleColor,
    this.size = 50.0, // Default size is 50.0
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        color: circleColor,
        shape: BoxShape.circle,
      ),
      child: Center(
        child: Icon(
          icon,
          color: iconColor,
          size: size * 0.5, // Icon size is half of the circle size by default
        ),
      ),
    );
  }
}

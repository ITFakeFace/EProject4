import 'package:flutter/material.dart';

class CircleCharacter extends StatelessWidget {
  final String character;
  final Color circleColor;
  final double size;

  const CircleCharacter({
    Key? key,
    required this.character,
    required this.circleColor,
    this.size = 50.0,
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
        child: Text(
          character,
          style: TextStyle(
            color: Colors.white,
            fontSize: size * 0.5,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }
}

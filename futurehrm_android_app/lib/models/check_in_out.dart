import 'package:hive/hive.dart';

part 'check_in_out.g.dart';

@HiveType(typeId: 2)
class CheckInOut extends HiveObject {
  @HiveField(0)
  int? id;

  @HiveField(1)
  int staffId;

  @HiveField(2)
  String staffCode;

  @HiveField(3)
  DateTime checkInDay;

  @HiveField(4)
  DateTime checkInAt;

  @HiveField(5)
  bool type;

  @HiveField(6)
  String? image;

  CheckInOut({
    this.id,
    required this.staffId,
    required this.staffCode,
    required this.checkInDay,
    required this.checkInAt,
    required this.type,
    this.image,
  });

  factory CheckInOut.fromMap(Map<String, dynamic> json) {
    return CheckInOut(
      id: json['id'],
      staffId: json['staffId'],
      staffCode: json['staffCode'],
      checkInDay: DateTime.fromMillisecondsSinceEpoch(json['checkInDay']),
      checkInAt: DateTime.fromMillisecondsSinceEpoch(json['checkInAt']),
      type: json['type'],
      image: json['image'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      "id": id,
      "staffId": staffId,
      "staffCode": staffCode,
      "checkInDay": checkInDay.toIso8601String(),
      "checkInAt": checkInAt.toIso8601String(),
      "type": type,
      "image": image,
    };
  }
}

import 'package:hive/hive.dart';

// part 'education.g.dart';

@HiveType(typeId: 2)
class Education extends HiveObject {
  @HiveField(0)
  int? id;

  @HiveField(1)
  int? staffId;

  @HiveField(2)
  int? level;

  @HiveField(3)
  String? levelName;

  @HiveField(4)
  String? school;

  @HiveField(5)
  String? fieldOfStudy;

  @HiveField(6)
  int? graduatedYear;

  @HiveField(7)
  String? grade;

  @HiveField(8)
  String? modeOfStudy;

  Education({
    this.id,
    required this.staffId,
    required this.level,
    this.levelName,
    this.school,
    this.fieldOfStudy,
    this.graduatedYear,
    this.grade,
    this.modeOfStudy,
  });

  factory Education.fromMap(Map<String, dynamic> map) {
    return Education(
      id: map['id'],
      staffId: map['staffId'],
      level: map['level'],
      levelName: map['levelName'],
      school: map['school'],
      fieldOfStudy: map['fieldOfStudy'],
      graduatedYear: map['graduatedYear'],
      grade: map['grade'],
      modeOfStudy: map['modeOfStudy'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      "id": id,
      "staffId": staffId,
      "level": level,
      "levelName": levelName,
      "school": school,
      "fieldOfStudy": fieldOfStudy,
      "graduatedYear": graduatedYear,
      "grade": grade,
      "modeOfStudy": modeOfStudy,
    };
  }
}

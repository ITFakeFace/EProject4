import 'package:hive/hive.dart';

// part 'department.g.dart';

@HiveType(typeId: 1)
class Department extends HiveObject {
  @HiveField(0)
  int? id;

  @HiveField(1)
  String? name;

  @HiveField(2)
  String? nameVn;

  @HiveField(3)
  bool del;

  Department({
    this.id,
    required this.name,
    required this.nameVn,
    required this.del,
  });

  factory Department.fromMap(Map<String, dynamic> map) {
    return Department(
      id: map['id'],
      name: map['name'],
      nameVn: map['nameVn'],
      del: map['del'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      "id": id,
      "name": name,
      "nameVn": nameVn,
      "del": del,
    };
  }
}

class ResponseData {
  dynamic data;
  String? message;
  int? status;
  bool? isSuccess = false;

  ResponseData({this.data, this.message, this.status, this.isSuccess});

  // Factory constructor to create an instance from a map
  factory ResponseData.fromMap(Map<String, dynamic> map) {
    return ResponseData(
      data: map['data'],
      message: map['message'],
      status: map['status'],
      isSuccess: map['isSuccess'],
    );
  }

  // Method to convert an instance to a map
  Map<String, dynamic> toMap() {
    return {
      'data': data,
      'message': message,
      'status': status,
      'isSuccess': isSuccess,
    };
  }
}

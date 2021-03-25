<?php

return [
    "exception_message" => "Thông báo ngoại lệ: :message",
    "exception_trace" => "Dấu vết ngoại lệ: :trace",
    "exception_message_title" => "Thông báo ngoại lệ",
    "exception_trace_title" => "Dấu vết ngoại lệ",

    "backup_failed_subject" => "Sao lưu không thành công của :application_name",
    "backup_failed_body" => "Quan trọng: Đã xảy ra lỗi khi sao lưu :application_name",

    "backup_successful_subject" => "Sao lưu mới thành công :application_name",
    "backup_successful_subject_title" => "Sao lưu mới thành công!",
    "backup_successful_body" => "Tin vui, một bản sao lưu mới của :application_name đã được tạo thành công trên đĩa có tên :disk_name.",

    "cleanup_failed_subject" => "Dọn dẹp các bản sao lưu của :application_name không thành công.",
    "cleanup_failed_body" => "Đã xảy ra lỗi khi dọn dẹp các bản sao lưu của :application_name",

    "cleanup_successful_subject" => "Dọn dẹp các bản sao lưu :application_name thành công",
    "cleanup_successful_subject_title" => "Dọn dẹp các bản sao lưu thành công!",
    "cleanup_successful_body" => "Dọn dẹp các bản sao lưu :application_name trên đĩa có tên :disk_name đã thành công.",

    "healthy_backup_found_subject" => "Các bản sao lưu cho :application_name trên disk :disk_name đều tốt",
    "healthy_backup_found_subject_title" => "Các bản sao lưu cho :application_name là tốt",
    "healthy_backup_found_body" => "Các bản sao lưu cho :application_name được coi là tốt. Làm tốt lắm!",

    "unhealthy_backup_found_subject" => "Quan trọng: Các bản sao lưu cho :application_name không lành mạnh",
    "unhealthy_backup_found_subject_title" => "Quan trọng: Các bản sao lưu cho :application_name không lành mạnh. :problem",
    "unhealthy_backup_found_body" => "Các bản sao lưu cho :application_name trên disk :disk_name không lành mạnh.",
    "unhealthy_backup_found_not_reachable" => "Không thể đạt được đích dự phòng. :error",
    "unhealthy_backup_found_empty" => "Không có bản sao lưu của ứng dụng này.",
    "unhealthy_backup_found_old" => "Bản sao lưu mới nhất được thực hiện vào: ngày được coi là quá cũ.",
    "unhealthy_backup_found_unknown" => "Xin lỗi, không thể xác định lý do chính xác.",
    "unhealthy_backup_found_full" => "Các bản sao lưu đang sử dụng quá nhiều dung lượng lưu trữ. Mức sử dụng hiện tại là :disk_usage cao hơn giới hạn cho phép của :disk_limit."
];

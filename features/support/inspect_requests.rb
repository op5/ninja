# This is from https://gitlab.com/gitlab-org/gitlab-foss/
# https://gitlab.com/gitlab-org/gitlab-foss/-/blob/a8b9852837c3ecde3148a7e989d53fd5ac025bc3/spec/support/inspect_requests.rb
# Ref: https://about.gitlab.com/blog/2017/12/19/moving-to-headless-chrome/
require_relative './wait_for_ajax'
require_relative '../middleware/request_inspector_middleware.rb'

module InspectRequests
  extend self
  include WaitForAjax

  def inspect_requests(inject_headers: {})
    TestingMiddleware::Testing::RequestInspectorMiddleware.log_requests!(inject_headers)

    yield

    wait_for_ajax
    TestingMiddleware::Testing::RequestInspectorMiddleware.requests
  ensure
    TestingMiddleware::Testing::RequestInspectorMiddleware.stop_logging!
  end
end

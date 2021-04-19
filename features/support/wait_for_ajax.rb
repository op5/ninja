module WaitForAjax
  extend self
  
  #Helper Functions
  def wait_for_ajax
    require "timeout"
    Timeout.timeout(Capybara.default_max_wait_time) do
      loop until finished_all_ajax_requests?
    end
  end
  
  def finished_all_ajax_requests?
    if Capybara.page.evaluate_script('typeof(jQuery)') == 'function'
      Capybara.page.evaluate_script('jQuery.active').zero?
    else
      return true    
    end
  end

  end
  